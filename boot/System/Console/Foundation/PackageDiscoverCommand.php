<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Foundation;

use Boot\System\Console\Command;
use Boot\App\PackageManifest;

class PackageDiscoverCommand extends Command
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'package:discover';

    /**
     * Имя и параметры консольной команды.
     *
     * Это имя используется для идентификации команды во время отложенной загрузки.
     *
     * @var string|null
     */
    protected static $defaultName = 'package:discover';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Перестроить кэш манифеста пакетов';

    /**
     * Выполнение консольной команды
     *
     * @param  \Boot\App\PackageManifest  $manifest
     * @return void
     */
    public function handle(PackageManifest $manifest)
    {
        $manifest->build();

        foreach (array_keys($manifest->manifest) as $package) {
            $this->line("Обнаруженный пакет: <info>{$package}</info>");
        }

        $this->info('Манифест пакетов создан успешно.');
    }
}
