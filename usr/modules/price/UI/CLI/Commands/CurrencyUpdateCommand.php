<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2023 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Commands;

use Boot\Support\Currency;
use Price\Models\CatalogPriceType;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class CurrencyUpdateCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:currency';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Обновления курсов валют';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $price_types = CatalogPriceType::where("course_auto", 1)->get();
        foreach ($price_types as $value) {
            $course = Currency::get_exchange()->get($value->currency);
            $value->update(["course" => $course]);
        }
    }
}
