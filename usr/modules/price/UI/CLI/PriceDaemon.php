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
use Boot\Support\Carbon;
use Price\Models\CatalogPrice;
use Price\Models\CatalogPriceRival;
use Price\UI\CLI\CatalogPriceDaemon;

class PriceDaemon
{
    use Key;

    static $price_type = 3;
    static $purchase_type = 1;

    static $key = ROOT . 'tmp/key/price.daemon.key';

    public $disable = true;

    public $limit = 500;
    public $date;


    protected $price;
    protected $purchase_price;
    protected $price_id;
    protected $purchase_price_id;

    public function get_key()
    {
        return static::$key;
    }

    public function get_price()
    {
        return $this->price;
    }

    public function start()
    {
        $query = $this->get_query();

        $query->where("type", "!=", 1)->where(function ($q) {
            $q->where('received_at', "<", Carbon::now()->startOfDay())->orWhereNull('received_at');
        })->inRandomOrder()->chunkById($this->limit, function ($items) {
            $this->prices_create();
            foreach ($items as $item) {
                if ($item->received_at < Carbon::now()->startOfDay()) {
                    $item->update(['received_at' => Carbon::now()]);
                    dump($item->id, $item->catalog->title);
                    try {
                        $this->price_daemon_type($item);
                    } catch (\Exception $e) {
                        setLog('Error', '0', 'daemon', $e->getMessage());
                        dump($e->getMessage());
                    }
                }
            }
            $this->prices_daemon();
        }, "id");
        return true;
    }

    public function start_by_ids($ids)
    {
        $items = CatalogPriceRival::with("catalog")->whereIn('id', $ids)->get();
        if (!empty($items)) {
            $this->prices_create();
            foreach ($items as $item) {
                if ($item->received_at < Carbon::now()->startOfDay()) {
                    $item->update(['received_at' => Carbon::now()]);
                    try {
                        $this->price_daemon_type($item);
                    } catch (\Exception $e) {
                        dump($e->getMessage());
                    }
                }
            }
            $this->prices_daemon();
        }
    }

    public function prices_daemon()
    {
        $this->price->daemon = 0;
        $this->purchase_price->daemon = 0;
        $this->price->save();
        $this->purchase_price->save();
        CatalogPriceDaemon::key_create();
    }

    public function prices_create($parent_price_id = null)
    {
        $this->price = static::create_price(["price_type" => static::$price_type, "parent_price_id" => $parent_price_id]);
        $this->price_id = $this->price->id;
        $this->purchase_price = static::create_price(["price_type" => static::$purchase_type, "parent_price_id" => $parent_price_id]);
        $this->purchase_price_id = $this->purchase_price->id;
    }

    public function price_daemon_type($item)
    {
        $PriceDaemonType = $this->get_class_name($item);
        if (class_exists($PriceDaemonType)) {
            $PriceDaemon = new $PriceDaemonType();
            $PriceDaemon->cpr = $item;
            $PriceDaemon->price_id = $this->price_id;
            $PriceDaemon->purchase_price_id = $this->purchase_price_id;
            $PriceDaemon->generate();
        }
    }

    public function get_class_name($item)
    {
        return "\Price\UI\CLI\Types\PriceDaemonType{$item->type}";
    }

    public function get_query()
    {
        $query = CatalogPriceRival::with("catalog")->whereHas('catalog', function ($query) {
            $query->where('hidden', 0);
        });
        if ($this->disable) {
            $query->whereNull("disable");
        }
        return $query;
    }

    static public function key_create()
    {
        $fp = fopen(static::$key, "w");
        fwrite($fp, date("Y-m-d"));
        fclose($fp);
    }

    public function check()
    {
        if (static::key_check()) {
            $context = file_get_contents(static::$key);
            if ($this->date == date("Y-m-d", strtotime($context))) {
                return false;
            } else {
                static::key_delete();
            }
        }
        return true;
    }

    public static function create_price($request = [])
    {
        //["price_type" => 1, ]
        //["daemon" => 1]
        $price_type = $request["price_type"];
        $date = date("d.m.Y H:i:s");
        $parent_id = !empty($request['parent_price_id'])  ? "(прайс основание #{$request['parent_price_id']})" : "";
        return CatalogPrice::create([
            "price_type" => $price_type, "comment" => "Взятие цен от {$date} {$parent_id}", "active" => 1, "uid" => 79, "auto" => 1, "daemon" => $request["daemon"] ?? 1,
        ]);
    }
}
