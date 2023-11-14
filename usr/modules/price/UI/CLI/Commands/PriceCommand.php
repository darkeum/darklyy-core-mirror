<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Commands;

use Price\UI\CLI\PriceDaemon;
use Boot\Abstracts\Commands\ConsoleCommand;

class PriceCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:get';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Взятие цен';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $PriceDaemon = new PriceDaemon;
        $result = $PriceDaemon->start();
        dump($result);
        if(!$result){
            dump('price:work');
            $this->call('price:work');
        }
    }
}
