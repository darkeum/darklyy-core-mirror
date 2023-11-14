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

use Boot\Abstracts\Commands\ConsoleCommand;
use Price\UI\CLI\CatalogPriceDiscountGroupsDaemon;

class PriceDiscountGroupsCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:discount_groups';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Обновление скидок по группам';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        while (1) {
            try {
                $CatalogPriceDiscountGroupsDaemon = new CatalogPriceDiscountGroupsDaemon;
                $CatalogPriceDiscountGroupsDaemon->start();
            } catch (\Exception $e) {
                setLog('Error', '0', 'daemon', $e->getMessage());
                dump($e->getMessage());
            }
        }
    }
}
