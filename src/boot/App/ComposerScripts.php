<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\App;

use Composer\Script\Event;

class ComposerScripts
{
    /**
     * Обработка события Composer post-install
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Обработка события Composer post-update
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Обработка события Composer post-autoload-dump
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postAutoloadDump(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Очистите кешированные файлы начальной загрузки Darklyy.
     *
     * @return void
     */
    protected static function clearCompiled()
    {
        $darklyy = new Application(getcwd());

        if (is_file($configPath = $darklyy->getCachedConfigPath())) {
            @unlink($configPath);
        }

        if (is_file($servicesPath = $darklyy->getCachedServicesPath())) {
            @unlink($servicesPath);
        }

        if (is_file($packagesPath = $darklyy->getCachedPackagesPath())) {
            @unlink($packagesPath);
        }
    }
}