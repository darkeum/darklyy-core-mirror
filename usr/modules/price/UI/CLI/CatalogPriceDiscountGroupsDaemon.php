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
use Catalog\Models\Catalog;
use Price\Models\CatalogPriceDiscountGroups;
use Price\Models\CatalogPriceDiscountOrganizations;

class CatalogPriceDiscountGroupsDaemon
{
    use Key;

    static $key = ROOT . 'tmp/key/price_discount_groups.daemon.key';

    public $g_d;
    public $i_d;

    public function start()
    {
        if (static::key_check()) {
            static::key_delete();
            $this->groups_list();
        }
    }

    public function groups_list()
    {
        CatalogPriceDiscountGroups::withTrashed()->where("daemon", 0)->chunk(200, function ($items) {
            foreach ($items as $item) {
                $this->g_d = $item;
                $this->groups();
            }
        });
    }

    public function groups()
    {
       Catalog::where("catalog_groups_id", $this->g_d->catalog_groups_id)->chunk(500, function ($d2) {
            foreach ($d2 as $c) {
                $this->i_d = $c;
                if (empty($this->g_d->deleted_at)) {
                    $this->item_add();
                } else {
                    $this->item_delete();
                }
            }
        });
        $this->g_d->daemon = 1;
        $this->g_d->saveQuietly();
    }

    public function item_add()
    {
        $item = $this->find_item();
        if ($item) {
            if ($item->auto == 1) {
                $item->update(["type" => $this->g_d->type]);
            }
        } else {
            CatalogPriceDiscountOrganizations::create(["oid" => $this->g_d->oid, "cid" => $this->i_d->id, "type" => $this->g_d->type, "auto" => 1]);
        }
    }

    public function item_delete()
    {
        $item = $this->find_item();
        if ($item && $item->auto == 1) {
            $item->delete();
        }
    }

    public function find_item()
    {
        return CatalogPriceDiscountOrganizations::where("oid", $this->g_d->oid)->where("cid", $this->i_d->id)->first() ?? false;
    }
}
