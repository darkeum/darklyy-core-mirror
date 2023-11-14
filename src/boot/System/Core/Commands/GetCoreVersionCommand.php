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
use Boot\System\Core\Foundation\Core;

class GetCoreVersionCommand extends ConsoleCommand
{
    /**
     * Имя и опции консольной команды.
     *
     * @var string
     */
    protected $signature = "core";

    /**
     * Описание команды
     *    
     * @var string    
     */
    protected $description = "Вывод текущей версии ядра";

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $this->info(Core::VERSION);
    }
}
