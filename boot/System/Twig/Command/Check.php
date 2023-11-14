<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Twig\Command;

use Boot\System\Console\Command;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Source;

class Check extends Command
{

    /**
     * Имя команды
     *
     * @var string
     */
    protected $name = 'twig:check';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Проверка синтаксиса шаблонов Twig';

    /**
     * Экземпляр TwigBridge
     * 
     * @var \Boot\System\Twig\Bridge
     */
    protected $twig;

    /**
     * Экземпляр Finder
     * 
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Получить экземпляр поиска файлов Twig в указанных каталогах.
     *
     * @param array $paths Пути для поиска файлов.
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function getFinder(array $paths)
    {
        $finder = (empty($this->finder)) ? Finder::create() : $this->finder;

        return $finder->files()->in($paths)->name('*.' . $this->laravel['twig.extension']);
    }

    /**
     * Установить средство поиска, используемое для поиска файлов Twig.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return void
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $this->twig = $this->darksander['twig'];
        $format     = $this->option('format');

        if (ftell(STDIN) === 0) {
            $template = '';

            while (!feof(STDIN)) {
                $template .= fread(STDIN, 1024);
            }

            if (!empty($template)) {
                return $this->display([$this->validate($template)], $format);
            }
        }

        $files   = $this->getFiles($this->argument('filename'), $this->option('file'), $this->option('directory'));
        $details = [];

        foreach ($files as $file) {
            try {
                $template = $this->getContents($file);
            } catch (LoaderError $e) {
                throw new RuntimeException(sprintf('Файл или директория "%s" не доступны для чтения', $file));
            }

            $details[] = $this->validate($template, $file);
        }

        return $this->display($details, $format);
    }

    /**
     * Получение массива файлов для проверки
     *
     * @param string $filename    Single file to check.
     * @param array  $files       Array of files to check.
     * @param array  $directories Array of directories to get the files from.
     *
     * @return array
     */
    protected function getFiles($filename, array $files, array $directories)
    {
        $search = $files;
        $paths  = $this->laravel['view']->getFinder()->getPaths();

        if (!empty($filename)) {
            $search[] = $filename;
        }

        if (!empty($directories)) {
            $search_directories = [];

            foreach ($directories as $directory) {
                foreach ($paths as $path) {
                    if (is_dir($path . '/' . $directory)) {
                        $search_directories[] = $path . '/' . $directory;
                    }
                }
            }

            if (!empty($search_directories)) {
                foreach ($this->getFinder($search_directories) as $file) {
                    $search[] = $file->getRealPath();
                }
            }
        }

        if (empty($search) && !empty($paths)) {
            foreach ($this->getFinder($paths) as $file) {
                $search[] = $file->getRealPath();
            }
        }

        return $search;
    }

    /**
     * Получить содержимое шаблона.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getContents($file)
    {
        return $this->darksander['twig.loader']->getSourceContext($file);
    }

    /**
     * Проверить шаблон
     *
     * @param \Twig\Source $template Шаблон Twig
     * @param string $file     Имя файла шаблона
     *
     * @return array
     */
    protected function validate($template, $file = null)
    {
        try {
            $this->twig->parse($this->twig->tokenize($template, $file));
        } catch (Error $e) {
            return [
                'template'  => $template,
                'file'      => $file,
                'valid'     => false,
                'exception' => $e,
            ];
        }

        return [
            'template'  => $template,
            'file'      => $file,
            'valid'     => true,
        ];
    }

    /**
     * Вывод результата проверки
     *
     * @param array  $details Результаты проверки для всех проверенных файлов.
     * @param string $format  Формат для вывода результатов. Поддерживает text или JSON.
     *
     * @throws \InvalidArgumentException Ошибка неизвестного формата
     *
     * @return int
     */
    protected function display(array $details, $format = 'text')
    {
        $verbose = $this->getOutput()->isVerbose();

        switch ($format) {
            case 'text':
                return $this->displayText($details, $verbose);

            case 'json':
                return $this->displayJson($details, $verbose);

            default:
                throw new InvalidArgumentException(sprintf('Формат файла "%s" не поддерживается.', $format));
        }
    }

    /**
     * Вывод результата проверки в текстовом формате
     *
     * @param array $details Результат валидации всех проверенных файлов.
     * @param bool  $verbose
     *
     * @return int
     */
    protected function displayText(array $details, $verbose = false)
    {
        $errors = 0;

        foreach ($details as $info) {
            if ($info['valid'] && $verbose) {
                $file = ($info['file']) ? ' in ' . $info['file'] : '';
                $this->line('<info>OK</info>' . $file);
            } elseif (!$info['valid']) {
                $errors++;
                $this->renderException($info);
            }
        }

        //Вывод итогового количества
        $success = count($details) - $errors;
        $total   = count($details);

        $this->comment(sprintf('%d/%d корректных файлов', $success, $total));

        return min($errors, 1);
    }

    /**
     *Вывод результата проверки в формате JSON
     *
     * @param array $details Результат валидации всех проверенных файлов.
     *
     * @return int
     */
    protected function displayJson(array $details)
    {
        $errors = 0;

        array_walk(
            $details,
            function (&$info) use (&$errors) {
                $info['file'] = (string) $info['file'];
                unset($info['template']);

                if (!$info['valid']) {
                    $info['message'] = $info['exception']->getMessage();
                    unset($info['exception']);
                    $errors++;
                }
            }
        );

        $this->line(json_encode($details, JSON_PRETTY_PRINT));

        return min($errors, 1);
    }

    /**
     * Вывод ошибок в консоль
     *
     * @param array Сведения о файле, который не удалось проверить.
     *
     * @return void
     */
    protected function renderException(array $info)
    {
        $file      = $info['file'];
        $exception = $info['exception'];

        $line  = $exception->getTemplateLine();
        $lines = $this->getContext($info['template'], $line);

        if ($file) {
            $this->line(sprintf('<error>Ошибка</error> в файле %s (на строке %s)', $file, $line));
        } else {
            $this->line(sprintf('<error>Ошибка</error> (в строке %s)', $line));
        }

        foreach ($lines as $no => $code) {
            $this->line(
                sprintf(
                    "%s %-6s %s",
                    $no == $line ? '<error>>></error>' : '  ',
                    $no,
                    $code
                )
            );

            if ($no == $line) {
                $this->line(sprintf('<error>>> %s</error> ', $exception->getRawMessage()));
            }
        }
    }

    /**
     * Захватывает окружающие строки вокруг исключения.
     *
     * @param \Twig\Source  $template Содержимое шаблона Twig.
     * @param string|int    $line     Строка, в которой произошло исключение.
     * @param int           $context  Номера строк вокруг строки, в которой произошло исключение.
     *
     * @return array
     */
    protected function getContext(Source $template, $line, $context = 3)
    {
        $lines    = explode("\n", $template->getCode());
        $position = max(0, $line - $context);
        $max      = min(count($lines), $line - 1 + $context);

        $result = [];

        while ($position < $max) {
            $result[$position + 1] = $lines[$position];
            $position++;
        }

        return $result;
    }

    /**
     * Аргументы команды
     *
     * @return void
     */
    protected function getArguments()
    {
        return [
            [
                'filename',
                InputArgument::OPTIONAL,
                'Имя файла или директории для проверки',
            ],
        ];
    }

    /**
     * Опции команды
     *
     * @return void
     */
    protected function getOptions()
    {
        return [
            [
                'file',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Проверка нескольких файлов. Относительно пути просмотра. Поддерживает точечный синтаксис.',
            ],
            [
                'directory',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Проверка нескольких каталогов. Относительно пути просмотра. Не поддерживает точечный синтаксис.',
            ],
            [
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Формат ответа. Поддерживается `text` или `json`.',
                'text',
            ],
        ];
    }

    /**
     * Вызов команды
     *
     * @return void
     */
    public function fire()
    {
        return $this->handle();
    }
}
