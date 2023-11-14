<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Core\Commands;

use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Boot\Support\Facades\Config;

class SeedTestingDataCommand extends ConsoleCommand
{
    /**
     * Имя и опции консольной команды.
     *
     * @var string
     */
    protected $signature = "core:seed-test";

    /**
     * Описание команды
     *    
     * @var string    
     */
    protected $description = "Заполнение данных для тестирования";

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $this->call('db:seed', [
            '--class' => Config::get('core.seeders.testing')
        ]);

        $this->info('Заполнение тестовыми данными успешно завершено');
    }
}
