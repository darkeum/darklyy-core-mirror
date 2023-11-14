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

use Price\UI\CLI\CatalogPriceDaemon;
use Boot\Abstracts\Commands\ConsoleCommand;

class CatalogPriceCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:catalog';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Обновление кэша цен каталога';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        CatalogPriceDaemon::key_delete();
        while (1) {
            try {
                if (CatalogPriceDaemon::key_check()) {
                    CatalogPriceDaemon::key_delete();
                    $CatalogPriceDaemon = new CatalogPriceDaemon;
                    $CatalogPriceDaemon->start();
                }
            } catch (\Exception $e) {
                setLog('Error', '0', 'daemon', $e->getMessage());
                dump($e->getMessage());
            }
        }
    }
}
