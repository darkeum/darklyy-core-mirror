<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Controllers;

use Boot\Support\Facades\DB;
use Boot\System\Http\Request;
use Price\UI\CLI\PriceGenerateType;
use Organizations\Model\Organizations;
use Price\Models\CatalogPriceDiscount;
use Boot\Controller\AbstractController;
use Price\Models\CatalogPriceDiscountGroups;
use Boot\Support\Facades\Request as RequestFacade;
use Price\Models\CatalogPriceDiscountOrganizations;

class PriceRivalController extends AbstractController
{
    static function client()
    {
        return view('price::manager.rival.client');
    }

    static function edit($id)
    {
        RequestFacade::session()->put('price.rival.client.edit', $id);
        $organization = Organizations::findOrFail($id);
        return view('price::manager.rival.edit', [
            'id' => $id,
            'organization' => $organization
        ]);
    }

    static function action()
    {
        return CatalogPriceDiscountOrganizations::action($_POST);
    }

    static function data(Request $request)
    {
        $type = $request->type;
        $list = PriceGenerateType::get_discount_name_list_rus();
        $selected = empty($type) ? "selected" : "";
        $data = ["<option {$selected}></option>"];
        $keys = [];
        foreach ($list as $key => $value) {
            $keys[] = $key;
        }
        $price_discount = CatalogPriceDiscount::whereIn('type', $keys)->where('cid', $request->cid)->get();
        $discount = [];
        foreach ($price_discount as $r) {
            $discount[$r->type] = $r->discount;
        }
        foreach ($list as $key => $value) {
            $selected = $key == $type ? "selected" : "";
            $disc = empty($discount[$key]) ? "" : $discount[$key];
            $data[] = "<option value='{$key}' {$selected}>{$value} ({$disc})</option>";
        }
        return implode("", $data);
    }

    static function group()
    {
        return view('price::manager.rival.group', [
            'types' => PriceGenerateType::get_discount_name_list_rus(),
            'list' => static::list("")
        ]);
    }

    static function oid(Request $request)
    {
        $res = [];
        $search = $request->q;
        $res = Organizations::Where('id', 'LIKE', "%$search%")
            ->orWhere('name', 'LIKE', "%$search%")
            ->orWhere('name_full', 'LIKE', "%$search%")
            ->orWhere('inn', 'LIKE', "%$search%")
            ->get();

        return response()->json($res);
    }

    static function list($id)
    {
        if (empty($id)) {
            return __('price::rival.oid.choose');
        } else {
            $rus = PriceGenerateType::get_discount_name_list_rus();
            $query = DB::table('catalog_groups')
                ->select('catalog_groups.*', 'catalog_price_discount_groups.type as discount_type')
                ->leftJoin('catalog_price_discount_groups', function ($join) use ($id) {
                    $join->on("catalog_price_discount_groups.catalog_groups_id", "=", "catalog_groups.id")
                        ->where("catalog_price_discount_groups.oid", '=', $id)
                        ->whereNull("catalog_price_discount_groups.deleted_at");
                })
                ->orderBy('catalog_price_discount_groups.id', 'ASC')->get();
            $all = '<div><label><input type="checkbox" value="all" class="item_input_all mr10">' . __('price::rival.all') . '</label></div>';
            $list_data = [$all];
            foreach ($query as $item) {
                $q = DB::table('catalog_price_discount_organizations')
                    ->leftJoin('catalog', "catalog_price_discount_organizations.cid", "=", "catalog.id")
                    ->where('catalog.catalog_groups_id', $item->id)
                    ->where('catalog_price_discount_organizations.oid', $id)
                    ->where('catalog_price_discount_organizations.auto', '0')
                    ->limit(1);
                $color = $q->count() > 0 ? "#3c80da" : "";
                $discount_type = empty($item->discount_type) ? '<i>' . __('price::rival.choose.empty') . '</i>' : "<b>{$rus[$item->discount_type]}</b>";
                $list_data[] = '<label class="item" style="color: ' . $color . '">
                <input type="checkbox" value="' . $item->id . '" class="item_input mr10">
                <div><a href="/administration/module/catalog_v2/groups/edit/' . $item->id . '" target="_blank">' . $item->name . '</a></div>
                <div>' . $discount_type . '</div>
              </label>';
            }
            return implode("", $list_data);
        }
    }

    static function set()
    {
        return CatalogPriceDiscountGroups::action($_POST);
    }

    static function discount()
    {
        return view('price::manager.rival.discount');
    }
}
