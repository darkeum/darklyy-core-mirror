<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI;

use Boot\Traits\Key;
use Price\UI\CLI\PriceDaemon;
use Price\Models\CatalogPrice;
use Price\Models\CatalogPriceTemp;

class CatalogPriceDaemon
{
    use Key;

    static $key = ROOT . "tmp/key/catalog_price.daemon.key";

    public function start()
    {
        $PriceDaemon = new PriceDaemon;
        CatalogPrice::where("daemon", 0)->where('active', 1)->chunkById(10, function ($catalog_prices) use (&$PriceDaemon, &$flag) {
            foreach ($catalog_prices as $catalog_price) {
                $catalog_price->detail()->chunkById(500, function ($catalog_price_details) use (&$catalog_price, &$PriceDaemon, &$flag) {
                    $items_type_1 = [];
                    foreach ($catalog_price_details as $catalog_price_detail) {
                        $cid = $catalog_price_detail->cid;
                        $p = CatalogPriceTemp::firstOrNew(["id" => $cid]);

                        $price = \Price::getOriginalPurchase($cid);
                        $p->price_purchase = empty($price->price) ? null : $price->data->id;
                        $p->price_type_purchase = empty($price->price_type) ? null : $price->price_type;

                        $price = \Price::getOriginalPriceList($cid);
                        $p->price_price = empty($price->price) ? null : $price->data->id;
                        $p->price_type_price = empty($price->price_type) ? null : $price->price_type;

                        $p->save();

                        //запоминаем если 1 тип
                        if ($catalog_price->type->type == "PRICE" && $catalog_price->auto == 0) {
                            $query = $PriceDaemon->get_query();
                            $item = $query->where("id", $cid)->first();
                            if (!empty($item) && $item->type == 1) {
                                $items_type_1[] = $item;
                            }
                        }
                    }
                    //обновление 1 типа цен
                    if (!empty($items_type_1)) {
                        $PriceDaemon->prices_create($catalog_price->id);
                        foreach ($items_type_1 as $value) {
                            $PriceDaemon->price_daemon_type($value);
                        }
                        $PriceDaemon->prices_daemon();
                    }
                });

                $catalog_price->daemon = 1;
                $catalog_price->save();
            }
        });
    }
}
