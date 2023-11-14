<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Models;

use Boot\Models\Model;
use Catalog\Models\Catalog;
use Price\Jobs\PriceRivalJob;

class CatalogPriceRival extends Model
{
    protected $casts = [
        'received_at'  => "datetime",
    ];

    protected $fillable = ["id", "type", "our_supplier_discount", "price_adjustments", "price_adjustments_price", "min_margin", "optimal_margin", "discount_installers_mrc", "correction_factor_price", "rival_link", "competing_links", "daemon", "disable", "iteration", "iteration_count", "received_at"];

    static function action($request, $work = true)
    {
        $id = $request["id"] ?? false;
        $item = static::find($id);
        if (empty($request["type"])) {
            if (!empty($item)) {
                $item->delete($id);
            }
        } else {
            if (isset($request["rival_link"])) {
                $request["rival_link"] = trim($request["rival_link"]);
            }
            if (empty($item)) {
                $item = static::create($request);
            } else {
                $request["daemon"] = 0;
                $request["iteration"] = null;
                $request["iteration_count"] = 0;
                $request["received_at"] = null;
                $item->update($request);
            }
            if ($item->type == 1) {
                PriceRivalJob::dispatch();
            }
        }
        if ($work) {
            app()->make(\Price\UI\CLI\Commands\PriceWorkCommand::class)->handle();
        }
        return $item;
    }

    static function getType()
    {
        return [1 => 1, 2, 3, 4, 5, 6];
    }

    public function catalog()
    {
        return $this->hasOne(Catalog::class, "id");
    }
}
