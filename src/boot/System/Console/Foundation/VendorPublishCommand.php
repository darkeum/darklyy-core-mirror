<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Foundation;

use Boot\System\Console\Command;
use Boot\System\Filesystem\Filesystem;
use Boot\Events\VendorTagPublished;
use Illuminate\Support\Arr;
use Boot\Support\ServiceProvider;
use Boot\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class VendorPublishCommand extends Command
{
    /**
     * Экземпляр файловой системы.
     *
     * @var \Boot\System\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Поставщик для публикации.
     *
     * @var string
     */
    protected $provider = null;

    /**
     * Теги для публикации.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Время начала выполнения команды.
     *
     * @var \Boot\Support\Carbon|null
     */
    protected $publishedAt;

    /**
     * Сигнатура консольной команды.
     *
     * @var string
     */

    protected $signature = 'vendor:publish
                    {--existing : Публиковать и перезаписывать только те файлы, которые уже были опубликованы}
                    {--force : Перезаписать любые существующие файлы}
                    {--all : Публикуйте активы для всех поставщиков услуг без запроса}
                    {--provider= : Поставщик услуг, у которого есть активы, которые вы хотите опубликовать}
                    {--tag=* : Один или несколько тегов с активами, которые вы хотите опубликовать}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Публикуйте любые публикуемые активы из пакетов поставщиков';

    /**
     * Указывает, следует ли обновлять даты миграции при публикации.
     *
     * @var bool
     */
    protected static $updateMigrationDates = true;

    /**
     * Создайте новый экземпляр команды.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Выполнение консольной команды.
     *
     * @return void
     */
    public function handle()
    {
        $this->publishedAt = now();

        $this->determineWhatShouldBePublished();

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }
    }

    /**
     * Определите поставщика или теги для публикации.
     *
     * @return void
     */
    protected function determineWhatShouldBePublished()
    {
        if ($this->option('all')) {
            return;
        }

        [$this->provider, $this->tags] = [
            $this->option('provider'), (array) $this->option('tag'),
        ];

        if (! $this->provider && ! $this->tags) {
            $this->promptForProviderOrTag();
        }
    }

    /**
     * Запросить поставщика или тег для публикации.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {
        $choices = $this->publishableChoices();

        $choice = windows_os()
            ? select(
                "Файлы какого поставщика или тега вы хотели бы опубликовать?",
                $choices,
                scroll: 15,
            )
            : search(
                label: "Файлы какого поставщика или тега вы хотели бы опубликовать?",
                placeholder: 'Поиск...',
                options: fn ($search) => array_values(array_filter(
                    $choices,
                    fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                )),
                scroll: 15,
            );

        if ($choice == $choices[0] || is_null($choice)) {
            return;
        }

        $this->parseChoice($choice);
    }

    /**
     * Варианты выбора, доступные через подсказку.
     *
     * @return array
     */
    protected function publishableChoices()
    {
        return array_merge(
            ['Все поставщики и теги'],
            preg_filter('/^/', '<fg=gray>Провайдер:</> ', Arr::sort(ServiceProvider::publishableProviders())),
            preg_filter('/^/', '<fg=gray>Тэг:</> ', Arr::sort(ServiceProvider::publishableGroups()))
        );
    }

    /**
     * Проанализируйте ответ, полученный в подсказке.
     *
     * @param  string  $choice
     * @return void
     */
    protected function parseChoice($choice)
    {
        [$type, $value] = explode(': ', strip_tags($choice));

        if ($type === 'Provider') {
            $this->provider = $value;
        } elseif ($type === 'Tag') {
            $this->tags = [$value];
        }
    }

    /**
     * Публикует активы для тега.
     *
     * @param  string  $tag
     * @return mixed
     */
    protected function publishTag($tag)
    {
        $pathsToPublish = $this->pathsToPublish($tag);

        if ($publishing = count($pathsToPublish) > 0) {
            $this->components->info(sprintf(
                'Публикация %sassets',
                $tag ? "[$tag] " : '',
            ));
        }

        foreach ($pathsToPublish as $from => $to) {
            $this->publishItem($from, $to);
        }

        if ($publishing === false) {
            $this->components->info('Нет доступных для публикации ресурсов для тега [' . $tag . '].');
        } else {
            $this->darklyy['events']->dispatch(new VendorTagPublished($tag, $pathsToPublish));

            $this->newLine();
        }
    }

    /**
     * Получите все пути для публикации.
     *
     * @param  string  $tag
     * @return array
     */
    protected function pathsToPublish($tag)
    {
        return ServiceProvider::pathsToPublish(
            $this->provider,
            $tag
        );
    }

    /**
     * Опубликовать указанный элемент из указанного места и в указанном месте.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishItem($from, $to)
    {
        if ($this->files->isFile($from)) {
            return $this->publishFile($from, $to);
        } elseif ($this->files->isDirectory($from)) {
            return $this->publishDirectory($from, $to);
        }

        $this->error("Не удается найти путь: <{$from}>");
    }

    /**
     * Опубликовать файл по указанному пути.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishFile($from, $to)
    {
        if ((! $this->option('existing') && (! $this->files->exists($to) || $this->option('force')))
            || ($this->option('existing') && $this->files->exists($to))
        ) {
            $to = $this->ensureMigrationNameIsUpToDate($from, $to);

            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            if ($this->option('existing')) {
                $this->components->twoColumnDetail(sprintf(
                    'Файл [%s] не найден',
                    str_replace(base_path() . '/', '', $to),
                ), '<fg=yellow;options=bold>ПРОПУЩЕНО</>');
            } else {
                $this->components->twoColumnDetail(sprintf(
                    'файл [%s] уже существует',
                    str_replace(base_path() . '/', '', realpath($to)),
                ), '<fg=yellow;options=bold>ПРОПУЩЕНО</>');
            }
        }
    }

    /**
     * Опубликовать каталог в указанном каталоге.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

        $this->moveManagedFiles($from, new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to, $visibility)),
        ]));

        $this->status($from, $to, 'директории');
    }

    /**
     * Переместить все файлы в указанный MountManager.
     *
     * @param  string  $from
     * @param  \League\Flysystem\MountManager  $manager
     * @return void
     */
    protected function moveManagedFiles($from, $manager)
    {
        foreach ($manager->listContents('from://', true)->sortByPath() as $file) {
            $path = Str::after($file['path'], 'from://');

            if (
                $file['type'] === 'file'
                && (
                    (! $this->option('existing') && (! $manager->fileExists('to://' . $path) || $this->option('force')))
                    || ($this->option('existing') && $manager->fileExists('to://' . $path))
                )
            ) {
                $path = $this->ensureMigrationNameIsUpToDate($from, $path);

                $manager->write('to://' . $path, $manager->read($file['path']));
            }
        }
    }

    /**
     * При необходимости создайте каталог для размещения опубликованных файлов.
     *
     * @param  string  $directory
     * @return void
     */
    protected function createParentDirectory($directory)
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Ensure the given migration name is up-to-date.
     *
     * @param  string  $from
     * @param  string  $to
     * @return string
     */
    protected function ensureMigrationNameIsUpToDate($from, $to)
    {
        if (static::$updateMigrationDates === false) {
            return $to;
        }

        $from = realpath($from);

        foreach (ServiceProvider::publishableMigrationPaths() as $path) {
            $path = realpath($path);

            if ($from === $path && preg_match('/\d{4}_(\d{2})_(\d{2})_(\d{6})_/', $to)) {
                $this->publishedAt->addSecond();

                return preg_replace(
                    '/\d{4}_(\d{2})_(\d{2})_(\d{6})_/',
                    $this->publishedAt->format('Y_m_d_His') . '_',
                    $to,
                );
            }
        }

        return $to;
    }

    /**
     * Вывести сообщение о состоянии в консоль.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path() . '/', '', realpath($from));

        $to = str_replace(base_path() . '/', '', realpath($to));

        $this->components->task(sprintf(
            'Копирование %s [%s] в [%s]',
            $type,
            $from,
            $to,
        ));
    }

    /**
     * Instruct the command to not update the dates on migrations when publishing.
     *
     * @return void
     */
    public static function dontUpdateMigrationDates()
    {
        static::$updateMigrationDates = false;
    }
}
