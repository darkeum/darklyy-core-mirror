<?php

namespace Sales2\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Http\Request;
use \Sales\Model\Sales;
use \Sales\Model\SalesDetail;


class SalesApiController extends AbstractController
{
    static function status_log($id)
    {
        return response(\Sales\Support\SalesDetailStatusLogSupport::get_view($id));
    }

    static function reserve_action($id)
    {
        return response(\Sales\Support\SalesSupport::smart_reserve($id));
    }
    static function reserve_create(Request $request)
    {
        $sales_id = $request->get("sales_id");
        $data = $request->get("data");
        $sales = \Sales\Model\Sales::find($sales_id);
        $result = [];

        if (isset($data["org"], $data["id"], $data["stock"])) {
            foreach ($data["org"] as $org) {
                $stock_id = $data["stock"][$org];
                foreach ($data["id"] as $id) {
                    $status_client = null;
                    $reserv = new \Reserv();
                    $count = \Sales\Support\SalesSupport::get_count_realization_left($id);
                    $reserv->set_reserv($id, "sales", $stock_id, $count);
                    if ($reserv->data->count == $reserv->count) {
                        $status_client = 6;
                    }
                    if ($reserv->future_count > 0) {
                        $status_client = 5;
                    }
                    if (!is_null($status_client)) {
                        \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => $status_client]);
                    }
                    if ($reserv->data->count == $reserv->count) {
                        \Sales\Support\SalesSupport::status_work($sales_id);
                    }
                }
            }
            foreach ($data["id"] as $id) {
                $result[] = [
                    "id" => $id,
                    "ndr" => \Sales2\Controller\SalesController::get_ndr($id, ["sales" => $sales]),
                ];
            }
        }
        return response($result);
    }
    static function reserve_delete(Request $request)
    {
        $sales_id = $request->get("sales_id");
        $data = $request->get("data");

        $sales = \Sales\Model\Sales::find($sales_id);

        $result = [];

        if (isset($data["org"], $data["id"], $data["stock"])) {
            foreach ($data["org"] as $org) {
                $stock_id = $data["stock"][$org];
                foreach ($data["id"] as $id) {
                    $reserv = new \Reserv();
                    $reserv->del_reserv($id, "sales", stock_id: $stock_id);
                }
            }
            foreach ($data["id"] as $id) {
                \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => 4]);
                $result[] = [
                    "id" => $id,
                    "ndr" => \Sales2\Controller\SalesController::get_ndr($id, ["sales" => $sales]),
                ];
            }
        }
        $sales->re_status_client();
        return response($result);
    }
    // static function reserve_create(Request $request){
    //     $sales_id = $request->get("sales_id");
    //     $ids = $request->get("id");
    //     $sales = \Sales\Model\Sales::find($sales_id);
    //     $stock_id = empty($sales->stock_id) ? false : $sales->stock_id;

    //     $re = function($id) use ($stock_id, $sales){
    //         $reserv = new \Reserv();
    //         $count = \Sales\Support\SalesSupport::get_count_realization_left($id);
    //         $reserv->set_reserv($id, "sales", $stock_id, $count);
    //         $clone_sales = new \Sales\CloneSales(["children_id" => $id]);
    //         $clone_sales->add_reserv(["count" => $count]);
    //         if($reserv->data->count == $reserv->count){
    //             $status_client = 6;
    //         }
    //         if($reserv->future_count > 0){
    //             $status_client = 5;
    //         }
    //         if(isset($status_client)){
    //             \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => $status_client]);
    //         }
    //         if($reserv->data->count == $reserv->count){
    //             \Sales\Support\SalesSupport::status_work($sales->id, ["sales" => $sales]);
    //         }
    //     };

    //     $result = [];
    //     if($stock_id){
    //         foreach($ids as $id){
    //             $re($id);
    //             $result[] = [
    //                 "id" => $id,
    //                 "ndr" => \Sales2\Controller\SalesController::get_ndr($id, ["sales" => $sales]),
    //             ];
    //         }
    //         $sales->re_status_client();
    //     }
    //     echo json_encode($result, JSON_UNESCAPED_UNICODE);
    // }
    // static function reserve_delete(Request $request){
    //     $sales_id = $request->get("sales_id");
    //     $ids = $request->get("id");

    //     $sales = \Sales\Model\Sales::find($sales_id);

    //     $re = function($id){
    //         $reserv = new \Reserv();
    //         $reserv->del_reserv($id, "sales");
    //         $clone_sales = new \Sales\CloneSales(["children_id" => $id]);
    //         $clone_sales->delete_reserv();

    //         \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => 4]);
    //     };

    //     $result = [];
    //     foreach($ids as $id){
    //         $re($id);
    //         $result[] = [
    //             "id" => $id,
    //             "ndr" => \Sales2\Controller\SalesController::get_ndr($id, ["sales" => $sales]),
    //         ];
    //     }
    //     $sales->re_status_client();
    //     echo json_encode($result, JSON_UNESCAPED_UNICODE);
    // }

    static function discount_oid(Request $request)
    {
        $sales_id = $request->get("sales_id");
        $sales_detail = $request->get("sales_detail");

        $sales = Sales::find($sales_id);
        $result = [];
        foreach ($sales_detail as $json_item) {
            $item = json_decode($json_item);
            $result[] = [
                "item" => $item,
                "discount" => \Price::getDiscount($item->cid, $sales->oid)
            ];
        }
        return response($result);
    }

    static function fast_change_sales(Request $request)
    {
        return response(\Sales\Support\SalesSupport::fast_change_sales(
            $request->get("pk"),
            $request->get("name"),
            $request->get("value")
        ));
    }

    static function change_sales_form($sales_id, Request $request)
    {
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        $sales->update($request->all());
        return response($sales);
    }

    static function sales_check($sales_id)
    {
        return response(\Sales\Support\SalesSupport::check($sales_id));
    }

    static function actualize_create(Request $request)
    {
        return response(\Sales\Support\SalesActualizeSupport::create(
            $request->get("sales_id"),
            $request->get("sales_detail_id"),
            [
                "notice" => $request->get("notice"),
            ]
        ));
    }

    static function send_purchase(Request $request)
    {
        return response(\Sales\Support\SalesSupplySupport::create(
            $request->get("sales_id"),
            $request->get("sales_detail_id"),
            [
                "status" => $request->get("status"),
                "org" => $request->get("org"),
            ]
        ));
    }

    static function check_can_delete(Request $request)
    {
        return response(check_can_delete_module($request->get("id"), \Sales\Model\SalesDetail::class));
    }

    static function check_zero_sum(Request $request)
    {
        $ids = $request->get("id");
        $sales_id = $request->get("sales_id");
        $detail = [];
        if (is_array($ids)) {
            $detail = \Sales\Models\SalesDetail::whereIn("id", $ids)->get();
        } elseif (!empty($sales_id)) {
            $detail = \Sales\Models\SalesDetail::where("sales_id", $sales_id)->get();
        }
        $result = ["status" => true, "items" => []];
        foreach ($detail as $item) {
            if ($item->total <= 0) {
                $result["items"][] = $item;
                $result["status"] = false;
            }
        }
        return response($result);
    }

    static function realization_create(Request $request)
    {
        return response(\Module::sales_to_realization(
            $request->get("sales_id"),
            [
                "sales_detail_id" => $request->get("sales_detail_id"),
            ],
        ));
    }

    static function comments_get($sales_id)
    {
        return response(\Boot\Models\CommentsAdmin::with(["user", "attach" => function ($q) {
            $q->where("module", "sales");
        }])->where("module", "sales")->where("module_id", $sales_id)->orderBy("id", "desc")->get());
    }

    static function comments_create(Request $request)
    {
        $comment = \Boot\Models\CommentsAdmin::create($request->all());
        if ($request->hasFile("file")) {
            $comment->attach = [\Boot\Models\Attach::save_file($request->file, "sales", $comment->id, folder_id: $request->get("module_id"))];
        }
        $comment->load("user");
        return response($comment);
    }

    static function messages_get($sales_id)
    {
        return response(\Sales\Support\SalesSupport::list_messages($sales_id));
    }

    static function messages_send(Request $request)
    {
        \Sales\Support\SalesSupport::send_message($request->get("sales_id"), $request->get("uid"), $request->get("role"));
        return response(["status" => true]);
    }

    static function end_sales_middleware($sales_id)
    {
        return response(\Sales\Support\SalesSupport::end_sales_middleware($sales_id));
    }

    static function end_sales(Request $request)
    {
        $sales = \Sales\Support\SalesSupport::end_sales(
            $request->get("sales_id"),
            $request->get("end"),
            $request->get("comment_end"),
        );
        return response(["sales" => $sales, "changed" => $sales->getChanges()]);
    }

    static function check_all_realization_and_transfer($sales_id)
    {
        $data = \Sales\Support\SalesSupport::check_all_realization_and_transfer($sales_id);
        return response([
            "status" => count($data) == 0,
            "data" => $data,
        ]);
    }

    static function clipboard_prepare(Request $request)
    {
        $sales_id = $request->get("sales_id");
        $new_items = json_decode($request->get("sales_detail"), true);
        $sales_detail = new \Illuminate\Database\Eloquent\Collection;;
        foreach ($new_items as $item) {
            $sales_detail->add(new \Sales\Model\SalesDetail($item));
        }
        $prepare_sales_detail = \Sales2\Controller\SalesController::prepare_sales_detail(\Sales\Model\Sales::find($sales_id), $sales_detail, ["add" => true]);
        return response($prepare_sales_detail);
    }

    static function extended_sort($sales_id, Request $request)
    {
        $re = $request->all();
        $re["data"] = \Sales\Model\SalesDetail::where("sales_id", $sales_id)->get();
        $extendedSort = new \ExtendedSort;
        $data = $extendedSort->sort($re);
        return response($data);
    }

    static function users_list_get($sales_id)
    {
        return response(\Sales\Model\SalesUsers::with("user")->where("sales_id", $sales_id)->get());
    }

    static function users_list_create(Request $request)
    {
        $new_user = \Sales\Model\SalesUsers::firstOrCreate([
            'sales_id' => $request->sales_id,
            'uid' => $request->uid,
            'type' => $request->type,
        ]);
        $new_user->user;
        return response($new_user);
    }

    static function users_list_delete($id)
    {
        $item = \Sales\Model\SalesUsers::find($id);
        if (!empty($item)) {
            $item->delete();
            return response($item);
        }
    }

    static function assembly_count(Request $request)
    {
        return response(\Sales\Support\SalesAssemblySupport::get_count($request->get("sales_detail_id")));
    }

    static function assembly_create(Request $request)
    {
        $data = \Sales\Support\SalesAssemblySupport::create(
            $request->get("sales_id"),
            $request->get("sales_detail_id"),
            ["count" => $request->get("count"),]
        );
        return response($data);
    }

    static function contract_get(Request $request)
    {
        $data = \Reestr\ReestrModule::load_pact_data([
            "oid" => $request->get("oid"),
            "module_out" => "sales",
            "pid_out" => $request->get("sales_id"),
            "onchange" => "dis_off_reestr()",
        ]);
        return response($data["template"]);
    }

    static function contract_save_($pid_in, $pid_out, $module_in)
    {
        $module_out = "sales";
        if (!empty($pid_out)) {
            \Boot\Models\Relations::where("pid_out", $pid_out)->where("module_out", $module_out)->where("module_in", $module_in)->delete();
            $rel = \Boot\Models\Relations::create([
                "pid_out" => $pid_out,
                "pid_in" => $pid_in,
                "module_out" => $module_out,
                "module_in" => $module_in,
            ]);
            return $rel;
        }
    }

    static function contract_save(Request $request)
    {
        $pid_out = $request->get("sales_id");
        $pid_in = $request->get("radio_reestr");
        $rel = static::contract_save_($pid_in, $pid_out, "reestr");
        return response($rel);
    }

    static function contract_check(Request $request)
    {
        $sales_id = $request->get("sales_id");
        try {
            if (\Sales\Support\SalesReestrSupport::check($sales_id)) {
                return response(["error" => []]);
            }
        } catch (\Error $e) {
            return response(["error" => [$e->getMessage()]]);
        }
    }

    static function contract_create(Request $request)
    {
        $sales_id = $request->get("sales_id");
        if (!empty($sales_id)) {
            $sales = \Sales\Model\Sales::findOrFail($sales_id);
            $reestr = \Reestr\Models\Reestr::create([
                "type" => 5,
                "oid" => $sales->oid,
                "org" => $sales->org,
                "auto" => 0,
                "status" => 1,
                "type_doc" => "out",
                "object" => $sales->object,
            ]);

            $contract_attach_id = \Sales\Support\SalesReestrSupport::get_contract(["sales_id" => $sales_id, "reestr_id" => $reestr->id,]);

            $reestr_doc = \Reestr\Support\ReestrSupport::add_doc([
                "reestr_id" => $reestr->id,
                "type" => 1,
                "doc" => $contract_attach_id,
            ]);

            \Boot\Models\Relations::where("pid_out", $sales_id)->where("module_out", "sales")->where("module_in", "reestr_spec")->delete();
            $rel = static::contract_save_($reestr->id, $sales_id, "reestr");
            return response([
                "reestr" => $reestr,
                "reestr_doc" => $reestr_doc,
                "rel" => $rel,
            ]);
        }
    }

    static function specification_create(Request $request)
    {
        $sales_id = $request->get("sales_id");
        $reestr_id = $request->get("reestr_id");
        if (!empty($sales_id) && !empty($reestr_id)) {

            $reestr_doc = \Reestr\Support\ReestrSupport::add_doc([
                "reestr_id" => $reestr_id,
                "type" => 2,
            ]);

            $data = $request->all();
            $data["reestr_doc_id"] = $reestr_doc->id;
            $specification_attach_id = \Sales\Support\SalesReestrSupport::get_spec($data);

            $reestr_doc->update(["doc" => $specification_attach_id]);

            $rel = static::contract_save_($reestr_doc->id, $sales_id, "reestr_spec");
            return response([
                "reestr_doc" => $reestr_doc,
                "rel" => $rel,
            ]);
        }
    }

    static function transfer_create(Request $request)
    {
        return response(\Module::sales_to_transfer($request->get("sales_id"), ["sales_detail_id" => $request->get("sales_detail_id")]));
    }

    static function load_max_discount(Request $request)
    {
        $data = [];
        foreach ($request->get("sales_detail") as $sales_detail_json) {
            $sales_detail = json_decode($sales_detail_json);
            $catalog = \Catalog_v2\Model\Catalog::find($sales_detail->cid);
            if (empty($catalog->price_data)) {
                $sales_detail->max_allowable_discount = "Не указано";
            } else {
                foreach ($catalog->price_data["data"] as $item) {
                    if (isset($item["field"]) && $item["field"] == "max_allowable_discount") {
                        $sales_detail->max_allowable_discount = $item["value"] . "%";
                        break;
                    }
                }
            }
            $data[] = $sales_detail;
        }
        return response($data);
    }

    static function confirm_supply_order_get(Request $request)
    {
        return response(\Supply\Model\SupplyOrder::with("supply_order_detail")->whereIn("id", $request->get("supply_order_id"))->get());
    }

    static function confirm_supply_order_send(Request $request)
    {
        $data = \Sales\Support\SalesSupport::supply_order_send_control(
            $request->get("sales_id"),
            $request->get("supply_order_id"),
            $request->get("status"),
            $request->get("comment")
        );
        return response($data);
    }

    static function status_client_block_send(Request $request)
    {
        $data = \Sales\Support\SalesSupport::status_client_send_control(
            $request->get("sales_id"),
            $request->get("status_client")
        );
        return response($data);
    }

    static function pay_get($sales_id)
    {
        $data = \Sales\Support\SalesSupport::getPayDetail($sales_id);
        return response($data->template);
    }

    static function purchase_data_get(Request $request)
    {
        $sales_detail = $request->get("sales_detail");
        $data = [];
        foreach ($sales_detail as $sd_) {
            $sd = json_decode($sd_);
            $data[] = [
                "uniqid" => $sd->uniqid ?? null,
            ] + SalesController::get_purchase_data($sd);
        }
        return response($data);
    }

    static function get_realization_sales($id)
    {
        $data = \Sales\Support\SalesDetailSupport::get_realization_sales($id);
        return response($data->template);
    }

    static function get_transfer_sales($id)
    {
        $data = \Sales\Support\SalesDetailSupport::get_transfer_sales($id);
        return response($data->template);
    }

    static function price_type_select(Request $request)
    {
        $type = $request->type;
        $price_type = \Price\Models\CatalogPriceType::find($type);
        if ($price_type->type == "PURCHASE") {
            $price = \Sales\Model\SalesDetail::getPurchase($request->id, [
                "cid" => $request->cid,
                "sales_detail" => json_decode($request->sales_detail),
            ]);
            return response(["price" => $price->price ?? 0]);
        } else {
            $price = \Price::getPrice($request["cid"]);
            return response(["price" => $price->price_list_]);
        }
    }

    static function files_get(Request $request)
    {
        return response(\Boot\Models\Attach::find($request->attach_id));
    }

    static function ndr_get(Request $request)
    {
        $sales = json_decode($request->get("sales"));
        $sales_detail = $request->get("sales_detail");
        $data = [];
        foreach ($sales_detail as $sd_) {
            $sd = json_decode($sd_);
            $data[] = [
                "id" => $sd->id ?? null,
                "uniqid" => $sd->uniqid ?? null,
                "data" => \Sales2\Controller\SalesController::get_ndr($sd->id, ["sales_detail" => $sd, "sales" => $sales]),
            ];
        }
        return response($data);
    }
}
