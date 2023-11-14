<?php

namespace Database\Seeders;

use Boot\System\Database\Seeder;
use \Price\Models\CatalogPriceType;

class CatalogPriceTypeSeeder extends Seeder
{
    /**
     * Запуск заполнения таблиц
     *
     * @return void
     */
    public function run()
    {
        $price = CatalogPriceType::firstOrNew(["id" => 1]);
        $price->title = "Закупочная цена";
        $price->currency = 643;
        $price->parent = NULL;
        $price->course = 1;
        $price->hidden = 0;
        $price->save();

        $price = CatalogPriceType::firstOrNew(["id" => 2]);
        $price->title = "Евро";
        $price->currency = 978;
        $price->parent = 3;
        $price->course = 83;
        $price->hidden = 1;
        $price->save();

        $price = CatalogPriceType::firstOrNew(["id" => 3]);
        $price->title = "Прайс-лист";
        $price->currency = 643;
        $price->parent = NULL;
        $price->course = 1;
        $price->hidden = 0;
        $price->save();

        $price = CatalogPriceType::firstOrNew(["id" => 4]);
        $price->title = "Произвольная";
        $price->currency = 643;
        $price->parent = NULL;
        $price->course = 1;
        $price->hidden = 1;
        $price->save();

        $price = CatalogPriceType::firstOrNew(["id" => 5]);
        $price->title = "Доллар";
        $price->currency = 840;
        $price->parent = 3;
        $price->course = 73;
        $price->hidden = 1;
        $price->save();
    }
}
