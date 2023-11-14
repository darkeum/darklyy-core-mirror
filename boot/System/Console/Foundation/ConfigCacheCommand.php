<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Console\Foundation;

use Boot\System\Console\Command;
use Boot\Contracts\Console\Kernel as ConsoleKernelContract;
use Boot\System\Filesystem\Filesystem;
use LogicException;
use Throwable;

class ConfigCacheCommand extends Command
{
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'config:cache';

    /**
     * Имя консольной команды.
     *
     * Это имя используется для идентификации команды во время отложенной загрузки.
     *
     * @var string|null
     */
    protected static $defaultName = 'config:cache';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Создания кэша настроек для более быстрой загрузки';

    /**
     * Экземпляр файловой системы.
     *
     * @var \Boot\System\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Создание нового экземпляра команды кэша конфигурации.
     *
     * @param  \Boot\System\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Обработчик команды
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function handle()
    {
        $this->call('config:clear');

        $config = $this->getFreshConfiguration();

        $configPath = $this->darksander->getCachedConfigPath();

        $this->files->put(
            $configPath,
            '<?php return ' . var_export($config, true) . ';' . PHP_EOL
        );

        try {
            require $configPath;
        } catch (Throwable $e) {
            $this->files->delete($configPath);

            throw new LogicException('Ваши файлы конфигурации не корректны.', 0, $e);
        }

        $this->info('Конфигурация успешно кэширована!');
    }

    /**
     * Загрузка новой копию конфигурации приложения.
     *
     * @return array
     */
    protected function getFreshConfiguration()
    {
        $app = require $this->darksander->bootstrapPath() . '/App.php';

        $app->useStoragePath($this->darksander->storagePath());

        $app->make(ConsoleKernelContract::class)->bootstrap();

        return $app['config']->all();
    }
}
