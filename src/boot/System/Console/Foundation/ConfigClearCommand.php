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
use Boot\System\Filesystem\Filesystem;

class ConfigClearCommand extends Command
{
    /**
     * Имя консольной команды.
     *
     * @var string
     */
    protected $name = 'config:clear';

    /**
     * Имя консольной команды.
     *
     * Это имя используется для идентификации команды во время отложенной загрузки.
     *
     * @var string|null
     */
    protected static $defaultName = 'config:clear';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Очитка кэша конфигурации';

    /**
     * Экземпляр файловой системы.
     *
     * @var \Boot\System\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Создание нового экземпляра команды очитки кэша конфигурации.
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
     */
    public function handle()
    {
        $this->files->delete($this->darksander->getCachedConfigPath());

        $this->info('Кэш конфигурации очищен!');
    }
}
