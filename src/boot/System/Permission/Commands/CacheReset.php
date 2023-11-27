<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Commands;

use Boot\System\Console\Command;
use Boot\System\Permission\PermissionRegistrar;

class CacheReset extends Command
{
    protected $signature = 'permission:cache-reset';

    protected $description = 'Сбросить кеш разрешений';

    public function handle()
    {
        if (app(PermissionRegistrar::class)->forgetCachedPermissions()) {
            $this->info('Кэш разрешений очищен.');
        } else {
            $this->error('Не удалось сбросить кеш.');
        }
    }
}
