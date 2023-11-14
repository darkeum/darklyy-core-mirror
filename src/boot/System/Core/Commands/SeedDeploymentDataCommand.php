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

class SeedDeploymentDataCommand extends ConsoleCommand
{
    /**
     * Имя и опции консольной команды.
     *
     * @var string
     */
    protected $signature = "core:seed-deploy";

    /**
     * Описание команды
     *    
     * @var string    
     */
    protected $description = "Исходные данные для первоначального развертывания.";

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $this->call('db:seed', [
            '--class' => Config::get('core.seeders.deployment')
        ]);

        $this->info('Исходные данные успешно заполнены');
    }
}
