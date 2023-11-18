<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Foundation;

use Boot\System\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Termwind\terminal;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Запустить приложение на внутреннем сервере дял разработки';

    /**
     * Текущее смещение порта
     *
     * @var int
     */
    protected $portOffset = 0;

    /**
     * Список обрабатываемых запросов и время их начала
     *
     * @var array<int, \Boot\Support\Carbon>
     */
    protected $requestsPool;

    /**
     * Указывает, отображалось ли выходное сообщение «Сервер работает...».
     *
     * @var bool
     */
    protected $serverRunningHasBeenDisplayed = false;

    /**
     * Переменные среды, которые должны быть переданы с хост-компьютера в процесс PHP-сервера.
     *
     * @var string[]
     */
    public static $passthroughVariables = [
        'APP_ENV',
        'IGNITION_LOCAL_SITES_PATH',
        'LARAVEL_SAIL',
        'PATH',
        'PHP_CLI_SERVER_WORKERS',
        'PHP_IDE_CONFIG',
        'SYSTEMROOT',
        'XDEBUG_CONFIG',
        'XDEBUG_MODE',
        'XDEBUG_SESSION',
    ];

    /**
     * Выполнение консольной команды.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $environmentFile = $this->option('env')
            ? base_path('.env') . '.' . $this->option('env')
            : base_path('.env');

        $hasEnvironment = file_exists($environmentFile);

        $environmentLastModified = $hasEnvironment
            ? filemtime($environmentFile)
            : now()->addDays(30)->getTimestamp();

        $process = $this->startProcess($hasEnvironment);

        while ($process->isRunning()) {
            if ($hasEnvironment) {
                clearstatcache(false, $environmentFile);
            }

            if (
                !$this->option('no-reload') &&
                $hasEnvironment &&
                filemtime($environmentFile) > $environmentLastModified
            ) {
                $environmentLastModified = filemtime($environmentFile);

                $this->newLine();

                $this->components->info('Окружение изменено. Перезапуск сервера...');

                $process->stop(5);

                $this->serverRunningHasBeenDisplayed = false;

                $process = $this->startProcess($hasEnvironment);
            }

            usleep(500 * 1000);
        }

        $status = $process->getExitCode();

        if ($status && $this->canTryAnotherPort()) {
            $this->portOffset += 1;

            return $this->handle();
        }

        return $status;
    }

    /**
     * Запуск нового серверного процесса.
     *
     * @param  bool  $hasEnvironment
     * @return \Symfony\Component\Process\Process
     */
    protected function startProcess($hasEnvironment)
    {
        $process = new Process($this->serverCommand(), public_path(), collect($_ENV)->mapWithKeys(function ($value, $key) use ($hasEnvironment) {
            if ($this->option('no-reload') || !$hasEnvironment) {
                return [$key => $value];
            }

            return in_array($key, static::$passthroughVariables) ? [$key => $value] : [$key => false];
        })->all());

        $process->start($this->handleProcessOutput());

        return $process;
    }

    /**
     * Получение полной команды сервера
     *
     * @return array
     */
    protected function serverCommand()
    {
        $server = file_exists(base_path('server.php'))
            ? base_path('server.php')
            : __DIR__ . '/../resources/server.php';

        return [
            (new PhpExecutableFinder)->find(false),
            '-S',
            $this->host() . ':' . $this->port(),
            $server,
        ];
    }

    /**
     * Получение хоста из запроса
     *
     * @return string
     */
    protected function host()
    {
        [$host] = $this->getHostAndPort();

        return $host;
    }

    /**
     * Получение порта из запроса
     *
     * @return string
     */
    protected function port()
    {
        $port = $this->input->getOption('port');

        if (is_null($port)) {
            [, $port] = $this->getHostAndPort();
        }

        $port = $port ?: 8000;

        return $port + $this->portOffset;
    }

    /**
     * Получение хоста и порта из запроса
     *
     * @return array
     */
    protected function getHostAndPort()
    {
        if (preg_match('/(\[.*\]):?([0-9]+)?/', $this->input->getOption('host'), $matches) !== false) {
            return [
                $matches[1] ?? $this->input->getOption('host'),
                $matches[2] ?? null,
            ];
        }

        $hostParts = explode(':', $this->input->getOption('host'));

        return [
            $hostParts[0],
            $hostParts[1] ?? null,
        ];
    }

    /**
     * Проверка, достигла ли команда максимального количества попыток выбора порта
     *
     * @return bool
     */
    protected function canTryAnotherPort()
    {
        return is_null($this->input->getOption('port')) &&
            ($this->input->getOption('tries') > $this->portOffset);
    }

    /**
     * Возвращает «вызываемый объект» для обработки выходных данных процесса.
     *
     * @return callable(string, string): void
     */
    protected function handleProcessOutput()
    {
        return fn ($type, $buffer) => str($buffer)->explode("\n")->each(function ($line) {
            if (str($line)->contains('Development Server (http')) {
                if ($this->serverRunningHasBeenDisplayed) {
                    return;
                }

                $this->components->info("Сервер запущен на [http://{$this->host()}:{$this->port()}].");
                $this->comment('  <fg=yellow;options=bold>Нажмите Ctrl+C для остановки сервера</>');

                $this->newLine();

                $this->serverRunningHasBeenDisplayed = true;
            } elseif (str($line)->contains(' Accepted')) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort] = [
                    $this->getDateFromLine($line),
                    false,
                ];
            } elseif (str($line)->contains([' [200]: GET '])) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort][1] = trim(explode('[200]: GET', $line)[1]);
            } elseif (str($line)->contains(' Closing')) {
                $requestPort = $this->getRequestPortFromLine($line);

                if (empty($this->requestsPool[$requestPort])) {
                    return;
                }

                [$startDate, $file] = $this->requestsPool[$requestPort];

                $formattedStartedAt = $startDate->format('Y-m-d H:i:s');

                unset($this->requestsPool[$requestPort]);

                [$date, $time] = explode(' ', $formattedStartedAt);

                $this->output->write("  <fg=gray>$date</> $time");

                $runTime = $this->getDateFromLine($line)->diffInSeconds($startDate);

                if ($file) {
                    $this->output->write($file = " $file");
                }

                $dots = max(terminal()->width() - mb_strlen($formattedStartedAt) - mb_strlen($file) - mb_strlen($runTime) - 9, 0);

                $this->output->write(' ' . str_repeat('<fg=gray>.</>', $dots));
                $this->output->writeln(" <fg=gray>~ {$runTime}s</>");
            } elseif (str($line)->contains(['Closed without sending a request'])) {
                // ...
            } elseif (!empty($line)) {
                $position = strpos($line, '] ');

                if ($position !== false) {
                    $line = substr($line, $position + 1);
                }

                $this->components->warn($line);
            }
        });
    }

    /**
     * Получение даты из данных PHP-сервера.
     *
     * @param  string  $line
     * @return \Boot\Support\Carbon
     */
    protected function getDateFromLine($line)
    {
        $regex = env('PHP_CLI_SERVER_WORKERS', 1) > 1
            ? '/^\[\d+]\s\[([a-zA-Z0-9: ]+)\]/'
            : '/^\[([^\]]+)\]/';

        $line = str_replace('  ', ' ', $line);

        preg_match($regex, $line, $matches);

        return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
    }

    /**
     * Получение порта запроса из данных PHP-сервера.
     *
     * @param  string  $line
     * @return int
     */
    protected function getRequestPortFromLine($line)
    {
        preg_match('/:(\d+)\s(?:(?:\w+$)|(?:\[.*))/', $line, $matches);

        return (int) $matches[1];
    }

    /**
     * Получение параметров консольной команды
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'Адрес хоста для сервера приложения', Env::get('SERVER_HOST', '127.0.0.1')],
            ['port', null, InputOption::VALUE_OPTIONAL, 'Порт для сервера приложения', Env::get('SERVER_PORT')],
            ['tries', null, InputOption::VALUE_OPTIONAL, 'Максимальное количество портов, с которых можно попытаться запустить', 10],
            ['no-reload', null, InputOption::VALUE_NONE, 'Не перезагружайте сервер разработки при изменении файла .env'],
        ];
    }
}
