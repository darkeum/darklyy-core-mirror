<?php

namespace Sales\Support;

class SalesDetailSupport{
    static public function recount_sales($id){
        $total_all = 0;
        $nds_all = 0;

        $sales = \Sales\Model\Sales::with("org_")->where("id", $id)->first();
        $sales_detail = \Sales\Model\SalesDetail::where("sales_id", $id)->get();

        foreach($sales_detail as $item){
            $p = \Price::get_all([
                "price" => $item->s_price, 
                "count" => $item->count, 
                "discount" => $item->discount, 
                "nds_includes" => $sales->org_->nds_includes, 
                "nds_type" => $sales->org_->nds,
            ]);
            $item->update([
                "discount_sum" => $p["discount_sum"],
                "type_nds" => $p["nds_type"],
                "nds" => $p["nds"],
                "total" => $p["total"],
            ]);
            $total_all += $p["total"];
            $nds_all += $p["nds"];
        }
        $sales->update([
            "total" => $total_all,
            "nds" => $nds_all,
        ]);

        return [
            "sales" => $sales,
            "sales_detail" => $sales_detail,
        ];
    }

    static public function victoria_order_tovar_add($request)
    {
        $id = isset($request["id"]) ? $request["id"] : false;
        $items = isset($request["items"]) ? $request["items"] : [];
        $sales_data = isset($request["sales_data"]) ? $request["sales_data"] : [];
        if ($id && count($items) > 0) {
            $add = [];
            $add["sales_id"] = $id;
            $add["sales_detail"] = [];
            foreach ($items as $cid => $item) {
                $price_data = \Price::getPrice($cid, ["oid" => $sales_data->oid ?? null]);
                $s_price = $price_data->price_list_;
                $add["sales_detail"][] = [
                    "cid" => $cid,
                    "count" => $item["count"],
                    "discount" => $price_data->price_discount->discount ?? 0,
                    "s_price" => $s_price,
                ];
            }
            static::mass_add($add);
        }
    }

    static function prepare_mass_add($request){
        $sales_id = $request["sales_id"];
        $sales = \Sales\Model\Sales::find($sales_id)->toArray();
        $orgInfo = getOrgInfo($sales["org"]);
        $position = \Sales\Model\SalesDetail::where("sales_id", $sales_id)->count();
        $items = [];
        foreach($request["sales_detail"] as $sd){
            $re = [];
            $cid = $sd["cid"];
            $catalog = \Catalog_v2\Model\Catalog::findOrFail($cid)->toArray();
            $re["sales_id"] = $sales_id;
            $re["cid"] = $cid;
            $re["s_sku"] = empty($sd["s_sku"]) ? $catalog["sku"] : $sd["s_sku"];
            $re["s_name"] = empty($sd["s_name"]) ? $catalog["title"] : $sd["s_name"];
            // $re["s_barcode"] = empty($sd["s_barcode"]) ? $catalog["barcode"] : $sd["s_barcode"];
            $price_type = empty($sd["price_type"]) ? 3 : $sd["price_type"];
            $re["price_type"] = $price_type;
            $re["discount"] = empty($sd["discount"]) ? 0 : $sd["discount"];
            $re["discount_sum"] = empty($sd["discount_sum"]) ? 0 : $sd["discount_sum"];
            $re["s_price_local"] = empty($sd["s_price_local"]) ? null : $sd["s_price_local"];
            if(empty($sd["s_price"])){
                if($price_type == 1){
                    $p = \Price::getPrice($cid);
                    $re["s_price"] = $p->price_purchase_;
                } else {
                    $p = \Price::getPrice($cid);
                    $re["s_price"] = $p->price_list_;
                }
            } else {
                $re["s_price"] = $sd["s_price"];
            }
            $re["count"] = empty($sd["count"]) ? 1 : $sd["count"];
            $data = \Price::get_all([
                "price" => $re["s_price"], 
                "count" => $re["count"], 
                "discount" => $re["discount"], 
                "nds_includes" => $sales["nds_includes"], 
                "nds_type" => $orgInfo["org"]["nds"]
            ]);
            $re["total"] = empty($sd["total"]) ? $data["total"] : $sd["total"];
            $re["status"] = empty($sd["status"]) ? 1 : $sd["status"];
            $re["type_nds"] = empty($sd["type_nds"]) ? $orgInfo["org"]["nds"] : $sd["type_nds"];
            $re["nds"] = empty($sd["nds"]) ? $data["nds"] : $sd["nds"];
            $position++;
            $re["position"] = empty($sd["position"]) ? $position : $sd["position"];
            $re["s_unit"] = empty($sd["s_unit"]) ? $catalog["unit"] : $sd["s_unit"];
            $items[] = $re;
        }
        return $items;
    }

    static function mass_add($request){
        //["sales_id" => "", "sales_detail" => [ ["cid" => ""], [] ]]
        $items = static::prepare_mass_add($request);
        $sales_id = $request["sales_id"];
        foreach($items as $item){
            \Sales\Model\SalesDetail::create($item);
        }
        static::recount_sales($sales_id);
    }

    static function replace($id, $request){
        $sales = \Sales\Model\SalesDetail::create($request);
        $new_id = $sales->id;
        \Sales\Model\SalesDetail::destroy($id);

        $clone_sales = new \Sales\CloneSales(["children_id" => $id]);
        $clone_sales->replace_children($id, $new_id);

        return $new_id;
    }

    static function get_realization_sales($sales_detail_id){
        $items = \Realization\Model\RealizationDetail::with("realization")->where("sdid", $sales_detail_id)->get();
        if($items->count() > 0){
            $template = "";
            foreach($items as $item){
                $template .= '<div><a style="color:'.($item->realization->active == 1 ? 'green' : 'red').';" href="/administration/module/realization/order/'.$item->rid.'" target="_blank">Реализация №'.$item->realization->numb.'</a></div>';
                $correct_items = \Realization\Model\CorrectDetail::where("realization_detail_id", $item->id)->get();
                if($correct_items->count() > 0){
                    foreach($correct_items as $ci){
                        $correct = $ci->correct;
                        echo '<div><a style="color:'.($correct->active == 1 ? 'green' : 'red').';" href="/administration/module/realization/correct_detail/'.$correct->id.'" target="_blank">Корректировка №'.$correct->numb.'</a></div>';
                    }
                }
            }
        } else {
            $template = "Реализации и корректировки отсутствуют";
        }
        return (object)[
            "items" => $items,
            "template" => $template,
        ];
    }

    static function get_transfer_sales($sales_detail_id){
        $items = \Storage\Model\StockTransferDetail::with("stock_transfer")->has("stock_transfer")->where("sdid", $sales_detail_id)->get();
        if($items->count() > 0){
            $template = "";
            foreach($items as $item){
                $template .= '<div><a style="color:'.($item->stock_transfer->active == 1 ? 'green' : 'red').';" href="/administration/module/storage/transfer/detail/'.$item->stock_transfer->id.'" target="_blank">Перемещение №'.$item->stock_transfer->numb.'</a></div>';
            }
        } else {
            $template = "Перемещении отсутствуют";
        }
        return (object)[
            "items" => $items,
            "template" => $template,
        ];
    }


}