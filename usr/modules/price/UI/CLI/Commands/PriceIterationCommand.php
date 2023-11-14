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

class PriceIterationCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:iteration';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Обновление цен каталога тех которые не взялись';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $PriceDaemon = new PriceDaemon;

        while (1) {
            try {
                $query = $PriceDaemon->get_query();
                $query = $query->whereNotNull("iteration");
                if ($query->exists()) {
                    $PriceDaemon->prices_create();
                    $query->chunk($PriceDaemon->limit, function ($items) use (&$PriceDaemon) {
                        foreach ($items as $item) {
                            $PriceDaemon->price_daemon_type($item);
                            $item->update(["daemon" => 1]);
                        }
                    });
                    $PriceDaemon->prices_daemon();
                }
                sleep(10800);
            } catch (\Exception $e) {
                setLog('Error', '0', 'daemon', $e->getMessage());
                dump($e->getMessage());
            }
        }
    }
}
