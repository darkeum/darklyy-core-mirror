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

use Victoria;
use Boot\System\Http\Request;
use Price\Models\CatalogPrice;
use Price\Models\CatalogPriceType;
use Price\Actions\PricePrintAction;
use Price\Models\CatalogPriceDetail;
use Boot\Controller\AbstractController;

class PriceController extends AbstractController
{
    static function index()
    {
        return view('price::manager.index');
    }

    static function search()
    {
        return view('price::manager.search');
    }

    static function add()
    {
        $types = CatalogPriceType::where('hidden', 0)->orWhereNotNull('parent')->get();
        return view('price::manager.add', [
            'types' => $types
        ]);
    }

    static function victoria($id)
    {
        $victoria = new Victoria;
        $victoria->id = $id;
        $victoria->url_add = route('admin.price.victoria.add', ['id' => $id]);
        $victoria->local_script = '$victoria.transfer_to_document_update = opener.update_prise_detail';
        echo $victoria->popup();
    }

    static function victoria_add($id)
    {
        $id = intval($_POST['id']);
        $items = $_POST['items'];
        foreach ($items as $item) {
            $CatalogPriceDetail = new CatalogPriceDetail;
            $CatalogPriceDetail->cid = $item["cid"];
            $CatalogPriceDetail->sum = 0;
            $CatalogPriceDetail->price_id = $id;
            $CatalogPriceDetail->save();
        }
    }

    static function show($id)
    {
        return view('price::manager.show', [
            'id' => $id
        ]);
    }

    static function detail_delete(Request $request)
    {
        if (!empty($request->id)) {
            $id = $request->id;
            CatalogPriceDetail::destroy($id);
        } else abort('413');
    }

    static function detail_save(Request $request)
    {
        if (!empty($request->saveData)) {
            $saveData = $request->saveData;
            $json = json_decode($saveData, true);
            foreach ($json as $key => $val) {
                $catalog_price_detail = CatalogPriceDetail::findOrFail($key);
                $catalog_price_detail->update($val);
            }
        } else abort('413');
    }

    static function comment(Request $request)
    {
        $item = CatalogPrice::findOrFail($request->pk);
        $item->comment = $request->value;
        $item->save();
        return $item;
    }

    static function print($id)
    {
        app(PricePrintAction::class)->run($id);
    }

    static function save(Request $request)
    {
        $open = empty($request->open) ? false : true;
        $catalog_price = CatalogPrice::create(["price_type" => $request->price_type]);
        if ($open) {
            return redirect()->route('admin.price.show', ['id' => $catalog_price->id]);
        } else {
            return redirect()->route('admin.price.add')->with('status', __('price::add.added'));
        }
    }
}
