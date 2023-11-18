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
use Boot\Support\Composer;
use Boot\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'about')]
class AboutCommand extends Command
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'about {--only= : Выбор раздела для отображения}
                {--json : Вывести информацию в формате JSON}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Вывод базовой информации о вашем приложении';

    /**
     * Экземпляр Composer
     *
     * @var \Boot\Support\Composer
     */
    protected $composer;

    /**
     * Данные для отображения
     *
     * @var array
     */
    protected static $data = [];

    /**
     * Зарегистрированные вызываемые объекты, которые добавляют пользовательские данные.
     *
     * @var array
     */
    protected static $customDataResolvers = [];

    /**
     * Создание нового экземпляра команды.
     *
     * @param  \Boot\Support\Composer  $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Выполнение консольной команды.
     *
     * @return int
     */
    public function handle()
    {
        $this->gatherApplicationInformation();

        collect(static::$data)
            ->map(
                fn ($items) => collect($items)
                    ->map(function ($value) {
                        if (is_array($value)) {
                            return [$value];
                        }

                        if (is_string($value)) {
                            $value = $this->laravel->make($value);
                        }

                        return collect($this->laravel->call($value))
                            ->map(fn ($value, $key) => [$key, $value])
                            ->values()
                            ->all();
                    })->flatten(1)
            )
            ->sortBy(function ($data, $key) {
                $index = array_search($key, ['Environment', 'Cache', 'Drivers']);

                return $index === false ? 99 : $index;
            })
            ->filter(function ($data, $key) {
                return $this->option('only') ? in_array($this->toSearchKeyword($key), $this->sections()) : true;
            })
            ->pipe(fn ($data) => $this->display($data));

        $this->newLine();

        return 0;
    }

    /**
     * Отображение информации о приложении
     *
     * @param  \Boot\Support\Collection  $data
     * @return void
     */
    protected function display($data)
    {
        $this->option('json') ? $this->displayJson($data) : $this->displayDetail($data);
    }

    /**
     * Отображение информации о приложении в подробном виде
     *
     * @param  \Boot\Support\Collection  $data
     * @return void
     */
    protected function displayDetail($data)
    {
        $data->each(function ($data, $section) {
            $this->newLine();

            $this->components->twoColumnDetail('  <fg=green;options=bold>' . $section . '</>');

            $data->pipe(fn ($data) => $section !== 'Environment' ? $data->sort() : $data)->each(function ($detail) {
                [$label, $value] = $detail;

                $this->components->twoColumnDetail($label, value($value));
            });
        });
    }

    /**
     * Отображение информации о приложении в формате JSON
     *
     * @param  \Boot\Support\Collection  $data
     * @return void
     */
    protected function displayJson($data)
    {
        $output = $data->flatMap(function ($data, $section) {
            return [
                (string) Str::of($section)->snake() => $data->mapWithKeys(fn ($item, $key) => [
                    $this->toSearchKeyword($item[0]) => value($item[1]),
                ]),
            ];
        });

        $this->output->writeln(strip_tags(json_encode($output)));
    }

    /**
     * Сбор информации о приложении
     *
     * @return void
     */
    protected function gatherApplicationInformation()
    {
        static::addToSection('Environment', fn () => [
            'Имя приложения' => config('app.name'),
            'Версия Darklyy' => $this->darklyy->version(),
            'Версия PHP' => phpversion(),
            'Версия Composer' => $this->composer->getVersion() ?? '<fg=yellow;options=bold>-</>',
            'Тип окружения' => $this->darklyy->environment(),
            'Отладка' => config('app.debug') ? '<fg=yellow;options=bold>ВКЛЮЧЕН</>' : 'ВЫКЛЮЧЕН',
            'URL' => Str::of(config('app.url'))->replace(['http://', 'https://'], ''),
            'Режим обслуживания' => $this->darklyy->isDownForMaintenance() ? '<fg=yellow;options=bold>ВКЛЮЧЕН</>' : 'ВЫКЛЮЧЕН',
        ]);

        static::addToSection('Cache', fn () => [
            'Config' => $this->darklyy->configurationIsCached() ? '<fg=green;options=bold>CACHED</>' : '<fg=yellow;options=bold>NOT CACHED</>',
            'Events' => $this->darklyy->eventsAreCached() ? '<fg=green;options=bold>CACHED</>' : '<fg=yellow;options=bold>NOT CACHED</>',
            'Routes' => $this->darklyy->routesAreCached() ? '<fg=green;options=bold>CACHED</>' : '<fg=yellow;options=bold>NOT CACHED</>',
            'Views' => $this->hasPhpFiles($this->laravel->storagePath('framework/views')) ? '<fg=green;options=bold>CACHED</>' : '<fg=yellow;options=bold>NOT CACHED</>',
        ]);

        $logChannel = config('logging.default');

        if (config('logging.channels.' . $logChannel . '.driver') === 'stack') {
            $secondary = collect(config('logging.channels.' . $logChannel . '.channels'))
                ->implode(', ');

            $logs = '<fg=yellow;options=bold>' . $logChannel . '</> <fg=gray;options=bold>/</> ' . $secondary;
        } else {
            $logs = $logChannel;
        }

        static::addToSection('Drivers', fn () => array_filter([
            'Broadcasting' => config('broadcasting.default'),
            'Cache' => config('cache.default'),
            'Database' => config('database.default'),
            'Logs' => $logs,
            'Mail' => config('mail.default'),
            'Octane' => config('octane.server'),
            'Queue' => config('queue.default'),
            'Scout' => config('scout.driver'),
            'Session' => config('session.driver'),
        ]));

        collect(static::$customDataResolvers)->each->__invoke();
    }

    /**
     * Определение, есть ли в данном каталоге файлы PHP.
     *
     * @param  string  $path
     * @return bool
     */
    protected function hasPhpFiles(string $path): bool
    {
        return count(glob($path . '/*.php')) > 0;
    }

    /**
     * Добавление дополнительных данных в вывод команды «about».
     *
     * @param  string  $section
     * @param  callable|string|array  $data
     * @param  string|null  $value
     * @return void
     */
    public static function add(string $section, $data, string $value = null)
    {
        static::$customDataResolvers[] = fn () => static::addToSection($section, $data, $value);
    }

    /**
     * Добавление дополнительных данных в вывод команды «about».
     *
     * @param  string  $section
     * @param  callable|string|array  $data
     * @param  string|null  $value
     * @return void
     */
    protected static function addToSection(string $section, $data, string $value = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                self::$data[$section][] = [$key, $value];
            }
        } elseif (is_callable($data) || ($value === null && class_exists($data))) {
            self::$data[$section][] = $data;
        } else {
            self::$data[$section][] = [$data, $value];
        }
    }

    /**
     * Получение разделов
     *
     * @return array
     */
    protected function sections()
    {
        return collect(explode(',', $this->option('only') ?? ''))
            ->filter()
            ->map(fn ($only) => $this->toSearchKeyword($only))
            ->all();
    }

    /**
     * Форматирование строки поиска.
     *
     * @param  string  $value
     * @return string
     */
    protected function toSearchKeyword(string $value)
    {
        return (string) Str::of($value)->lower()->snake();
    }
}
