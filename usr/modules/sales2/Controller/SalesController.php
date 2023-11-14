<?php

namespace Sales2\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Http\Request;
use \Sales2\Grid\IndexGrid;
use \Sales\Models\Sales;
use \Sales\Models\SalesDetail;

class SalesController extends AbstractController{
    static $namespace = "sales2";

    static function index(){
        $grid = new IndexGrid;
        display("@".static::$namespace."/manager/index.html.twig", [
            'grid' =>  $grid->render(),
        ]);
    }

    static function create(){
        $options = [
            "mod" => "select",
            "name" => "oid",
            "ajaxUrl" => "getOrganizations",
            "return" => true,
            "required" => true,
            "bootstrap" => true,
        ];
        $oid = [];
        $oid["select"] = oSelectCustom($options);
        $options["mod"] = "jsReturn";
        $oid["js"] = oSelectCustom($options);
        $object = [];
        $options["ajaxUrl"] = "getObjectToSelect";
        $object["js"] = oSelectCustom($options);
        $options["mod"] = "select";
        $options["name"] = "object";
        $object["select"] = oSelectCustom($options);
        display("@".static::$namespace."/manager/create", [
            "org" => \Organizations\Model\Org::with("organization")->get(),
            "sales_type" => Sales::getSalesType(),
            "oid" => $oid,
            "oid_create" => \FastCreate5::get("organization"),
            "sales_cat" => Sales::getSalesCat(),
            "date" => date("Y-m-d"),
            "object" => $object,
            "object_create" => \FastCreate5::get("object"),
            "source_appeal" => Sales::getSourceAppeal(),
            "market_segment" => Sales::getMarketSegment(),
            "by_field_activity" => Sales::getByFieldActivity(),
        ]);
    }

    static function store(Request $request){
        $re = $request->all();
        $re["uid"] = \Boot\Support\Facades\Auth::id();
        $sales = \Sales\Models\Sales::create($re);
		// $org_main = 1;
		// if(!empty($re["clone_active"]) && $re["org"] != $org_main){
		// 	$org = getOrgInfo($re["org"]);
		// 	$re["org"] = $org_main;
		// 	$re["clone_active"] = 0;
		// 	$re["oid"] = $org["org"]["oid"];
		// 	$re["is_clone"] = 1;
		// 	$clone_sales = \Sales\Model\Sales::create($re);
		// 	$sales->update(["clone" => $clone_sales->id]);
		// 	setcookie("stock_id_view", 1, 0, "/");
		// }
        header("Location: ".router("admin.sales.show", $sales->id));
    }

    static function get_stock_id_view($sales){
        return $_COOKIE["stock_id_view"] ?? $sales->stock_id;
    }

    private static function get_data($id, Request $request, $display){
        $sales = Sales::findOrFail($id);
        $sales->user;
        $sales->user_maintenance;
        $sales->object_;
        $sales->organization;

        if($display != "head"){
            $sales_detail = \Sales\Models\SalesDetail::where("sales_id", $id)->orderBy("position")->get();
        }

        $sales->stock_id_view = static::get_stock_id_view($sales);

        if($display == "index" || $display == "head"){
            $contract_rel = \Boot\Models\Relations::where("module_in", "reestr")->where("module_out", "sales")->where("pid_out", $id)->first();
            if(!empty($contract_rel) && empty(\Reestr\Models\Reestr::find($contract_rel->pid_in))){
                $contract_rel = null;
            }
            $sales->contract = (object)[
                "view" => empty($contract_rel) ? "Отсутствует" : \Reestr\ReestrModule::pact_template($contract_rel->pid_in),
                'view_new' => empty($contract_rel) ? "Отсутствует" : \Reestr\ReestrModule::pact_template_new($contract_rel->pid_in),
                "exists" => !empty($contract_rel),
                "id" => $contract_rel->pid_in ?? null,
            ];
            $spec = "Отсутствует";
            $spec_exists = false;
            if(!empty($contract_rel)){
                $spec_rel = \Boot\Models\Relations::where("module_in", "reestr_spec")->where("module_out", "sales")->where("pid_out", $id)->first();
                if(!empty($spec_rel)){
                    $spec_doc = \Reestr\Models\ReestrDoc::find($spec_rel->pid_in);
                    if(!empty($spec_doc)){
                        $spec = '<a target="_blank" href="/administration/module/reestr/'.$contract_rel->pid_in.'">№'.$spec_doc->spec_numb.' от '.date('d.m.Y', strtotime($spec_doc->date_create)).'</a>';
                        $spec_exists = true;
                    }
                }
            }
            $sales->specification = (object)[
                "view" => $spec,
                "exists" => $spec_exists,
            ];
        }

        if($display == "index" || $display == "body"){
            $priceTypeSelect = new \PriceTypeSelect;
            $sales_detail = static::prepare_sales_detail($sales, $sales_detail, [
                "priceTypeSelect" => $priceTypeSelect, "ndr_load" => true, "purchase_load" => true,
            ]);
            $sales->local_date_delivery_select = \Sales\Model\SalesDetail::getLocalDateDeliverySource();
            $sales->local_date_delivery_list = \Sales\Model\SalesDetail::getLocalDateDelivery();
            $price_type_list = [];
            foreach($priceTypeSelect->get_prepare_data() as $item){
                $price_type_list[ $item->id ] = $item;
            }
            $sales->price_type_list = $price_type_list;
            $sales->purchase = [];
        }

        switch($display){
            case "index":
                $victoria_append = new \Victoria;
                $victoria_replace = new \Victoria;
				$victoria_append->link = router("admin.sales.victoria_append", $id);
                $victoria_replace->link = '${link}';
                $btn = <<<HTML
                    <div class="btn-group">
						<button type="button" class="btn btn-success" @click="appendGoods">Добавить товар</button>
						<button type="button" class="btn btn-primary" @click="refresh">Обновить</button>
						<button type="button" class="btn btn-success" @click="save" :disabled="!is_changed">Сохранить</button>
					</div>
HTML;
                $options = [
                    "mod" => "select",
                    "name" => "uid",
                    "select_id" => "users_modal_select",
                    "ajaxUrl" => "getUsersAllToSelect",
                    "return" => true,
                    "required" => true,
                    "bootstrap" => true,
                ];
                $select_users_all = [];
                $select_users_all["select"] = oSelectCustom($options);
                $options["mod"] = "jsReturn";
                $select_users_all["js"] = oSelectCustom($options);

                $oid_select = [];
                $options["mod"] = "select";
                $options["name"] = "oid";
                $options["ajaxUrl"] = "getOrganizations";
                $oid_select["select"] = oSelectCustom($options);
                $options["mod"] = "jsReturn";
                $oid_select["js"] = oSelectCustom($options);

                $uid_select = [];
                $options["mod"] = "select";
                $options["name"] = "uid";
                $options["ajaxUrl"] = "getUsersToSelect";
                $uid_select["select"] = oSelectCustom($options);
                $options["mod"] = "jsReturn";
                $uid_select["js"] = oSelectCustom($options);

                $uid_maintenance_select = [];
                $options["mod"] = "select";
                $options["name"] = "uid_maintenance";
                $options["ajaxUrl"] = "getUsersToSelect";
                $uid_maintenance_select["select"] = oSelectCustom($options);
                $options["mod"] = "jsReturn";
                $uid_maintenance_select["js"] = oSelectCustom($options);


                $object_select = [];
                $options["mod"] = "select";
                $options["name"] = "object";
                $options["ajaxUrl"] = "getObjectToSelect";
                $object_select["select"] = oSelectCustom($options);
                $options["mod"] = "jsReturn";
                $object_select["js"] = oSelectCustom($options);

                $sales_type_source = [];
                foreach(Sales::getSalesType() as $value => $text){
                    if($value != 0)
                    $sales_type_source[] = ["value" => $value, "text" => $text];
                }
                $org_source = [];
                $orgs = \Organizations\Model\Org::with(["organization", "stock"])->get();
                foreach($orgs as $item){
                    $org_source[] = ["value" => $item->id, "text" => $item->organization->name];
                }
                $stock = \Stocks\Models\Stock::get_cache_all();
                $stock_view_source = [];
                foreach($stock as $item){
                    $stock_view_source[] = ["value" => $item->id, "text" => $item->name];
                }
                $stock_source = [];
                foreach($stock->where("org", $sales->org) as $item){
                    $stock_source[] = ["value" => $item->id, "text" => $item->name];
                }
                $source_appeal_source = [];
                foreach(Sales::getSourceAppeal() as $value => $text){
                    $source_appeal_source[] = ["value" => $value, "text" => $text];
                }
                $market_segment_source = [];
                foreach(Sales::getMarketSegment() as $value => $text){
                    $market_segment_source[] = ["value" => $value, "text" => $text];
                }
                $by_field_activity_source = [];
                foreach(Sales::getByFieldActivity() as $value => $text){
                    $by_field_activity_source[] = ["value" => $value, "text" => $text];
                }
                $sales_cat_source = [];
                foreach(Sales::getSalesCat() as $value => $text){
                    if($value != 0)
                    $sales_cat_source[] = ["value" => $value, "text" => $text];
                }

                $fastBar = [];
                if($sales->is_clone == 0){
                    $fastBar = [
                        "template" => \FastBar::template([
                            "container" => "#sales_table",
                            "module" => 'sales_detail',
                            "id" => $id,
                            "hotkeys" => $btn,
                            "clipboard" => "clipboard_sales",
                        ]),
                        "script" => \FastBar::script(),
                        "style" => \FastBar::style(),
                    ];
                }

                return [
                    "id" => $id,
                    "sales" => $sales,
                    "sales_detail" => $sales_detail,
                    "comments_count" => \Boot\Models\CommentsAdmin::where("module", "sales")->where("module_id", $id)->count(),
                    "price_type_script" => \PriceTypeSelect::script(),
                    "victoria_append" => $victoria_append->open_func(),
                    "victoria_replace" => $victoria_replace->open_func(),
                    "btn" => $btn,
                    "fastBar" => $fastBar,
                    "extendedSort" => \ExtendedSort5::get_init(),
                    "oid_create" => \FastCreate5::get("organization"),
                    "object_create" => \FastCreate5::get("object"),
                    "select_users_all" => $select_users_all,
                    "oid_select" => $oid_select,
                    "uid_select" => $uid_select,
                    "uid_maintenance_select" => $uid_maintenance_select,
                    "object_select" => $object_select,
                    "sales_type_source" => $sales_type_source,
                    "org_source" => $org_source,
                    "orgs" => $orgs,
                    "stock_view_source" => $stock_view_source,
                    "stock_source" => $stock_source,
                    "source_appeal_source" => $source_appeal_source,
                    "market_segment_source" => $market_segment_source,
                    "by_field_activity_source" => $by_field_activity_source,
                    "sales_cat_source" => $sales_cat_source,
                    "ndr" => \NDR5::get(),
                ];
            break;
            case "head":
                return ["sales" => $sales];
            break;
            case "body":
                return ["sales" => $sales, "sales_detail" => $sales_detail];
            break;
        }
    }

    static function show($id, Request $request){
        $display = $request->get("display") ?? "index";
        $data = static::get_data($id, $request, $display);
        switch($display){
            case "index":
                display("@".static::$namespace."/manager/show/".$display, $data);
            break;
            case "head":
            case "body":
                return response($data);
            break;
        }
    }

    static function prepare_sales_detail($sales, \Illuminate\Database\Eloquent\Collection $sales_detail, $option = []){
        $priceTypeSelect = $option["priceTypeSelect"] ?? new \PriceTypeSelect;
        $supply_o_tov_class = isset($_GET["supply_o_tov"]) ? $_GET["supply_o_tov"] : [];
        $sales_detail->load("catalog");
        $key = 0;
        foreach($sales_detail as $sd){
            if($option["ndr_load"] ?? false){
                $sd->ndr = ["load" => true];
            } else {
                $sd->ndr = static::get_ndr($sd->id, ["sales_detail" => $sd, "sales" => $sales, "load" => false]);
            }

            $priceTypeSelect->type = $sd->price_type;
            $priceTypeSelect->cid = $sd->cid;
            $priceTypeSelect->id = $sd->id;
            $options = $priceTypeSelect->get_options();
            $sd->price_type_option = $options["option"];
            $sd->price_type_option_ = implode("", $options["option"]);
            $sd->price_type = $options["price_type"];
            $sd->nds_proc = \Price::getNds($sd->type_nds);


            if($option["purchase_load"] ?? false){
                $sd->purchase = ["load" => true];
            } else {
                $sd->purchase = static::get_purchase_data($sd, $option);
            }

            $sd->position = $key + 1;
            $sd->isSupplied = in_array($sd->id, $supply_o_tov_class) ? true : false;
            $sd->uniqid = uniqid();
            $sd->unit_name = get_unit('data', $sd->s_unit ,'sname');
            $date_post_data = SalesDetail::getDatePost($sd->id, ["sales_detail" => $sd]);
            $sd->date_post_view = $date_post_data->date_post;

            if($sales->sales_type == 2){
                $sd->count_transfer = \Sales\Support\SalesSupport::get_transfer_active_count($sd->id);
            }
            $key++;
        }
        return $sales_detail;
    }

    static function get_purchase_data($sales_detail, $option = []){
        $data = SalesDetail::getPurchase($sales_detail->id ?? null, ["sales_detail" => $sales_detail]);
        return [
            'price' => $data->price,
            'date'  => $data->date,
            'color'  => $data->color,
            'files'  => $data->files,
        ];
    }

    static function get_ndr($id, $option = []){
        $sales_detail = $option["sales_detail"] ?? SalesDetail::find($id);
        $sales = $option["sales"] ?? Sales::find($sales_detail->id);
        $ndr_config = [
            "cid" => $sales_detail->cid,
            "stock_id" => static::get_stock_id_view($sales),
            "module" => "sales",
            "pid" => $sales_detail->id,
            // "returnType" => "view",
            "returnType" => "all",
            "space" => "&nbsp;",
        ];
        // $ndr = ["view" => get_ndr($ndr_config)];
        // $ndr_config["returnType"] = "array";
        // $ndr_ = get_ndr($ndr_config);
        // $ndr["data"] = $ndr_;
        // return $ndr;
        return get_ndr($ndr_config);
    }

    // static function edit($id){

    // }

    // static function update(Request $request, $id){

    // }

    // static function destroy($id){

    // }

    static function victoria_append($id){
		$victoria = new \Victoria;
		$victoria->id = $id;
		$victoria->local_script = <<<HTML
            \$victoria.transfer_to_document_add = async form => {
                await opener.append_goods(form);
            };
            \$victoria.transfer_to_document_update = () => {};
HTML;
		echo $victoria->popup();
    }

    static function victoria_replace($id){
		$victoria = new \Victoria;
		$victoria->id = $id;
        $victoria->transfer_to_document_disabled = true;
		$victoria->local_script = <<<HTML
            \$victoria.add_basket_action = async function(cid){
                this.close();
                await opener.replace_good(this.id, cid);
			}
HTML;
		echo $victoria->popup();
    }

    static function victoria_append_prepare(Request $request){
        $data = $request->all();
        $add = [];
		$add["sales_id"] = $data["id"];
		$add["sales_detail"] = [];
        $items = $data["items"] ?? [];
		foreach($items as $cid => $item){
			$add["sales_detail"][] = [
				"cid" => $cid,
				"count" => $item["count"],
				"discount" => $item["discount"],
			];
		}
		$new_items = \Sales\Support\SalesDetailSupport::prepare_mass_add($add);
        $sales_detail = new \Illuminate\Database\Eloquent\Collection;
        foreach($new_items as $item){
            $sales_detail->add(new \Sales\Model\SalesDetail($item));
		}
        $prepare_sales_detail = static::prepare_sales_detail(\Sales\Model\Sales::find($data["id"]), $sales_detail, ["add" => true]);
        echo json_encode($prepare_sales_detail, JSON_UNESCAPED_UNICODE);
    }

    static function victoria_replace_prepare(Request $request){
        $add = [];
        $sales_id = $request->get("sales_id");
		$add["sales_id"] = $sales_id;
		$add["sales_detail"] = [
            ["cid" => $request->get("cid")]
        ];
		$new_items = \Sales\Support\SalesDetailSupport::prepare_mass_add($add);
        $sales_detail = new \Illuminate\Database\Eloquent\Collection;
        foreach($new_items as $item){
            $sales_detail->add(new \Sales\Model\SalesDetail($item));
		}
        $prepare_sales_detail = static::prepare_sales_detail(\Sales\Model\Sales::find($sales_id), $sales_detail, ["add" => true]);
        echo json_encode($prepare_sales_detail[0], JSON_UNESCAPED_UNICODE);
    }

    static function save(Request $request){
        $sales_id = $request->get("sales_id");
        $sales_detail = json_decode($request->get("sales_detail"));
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        $update = false;
        $reserv = new \Reserv();
        $sales_detail_items = [];
        foreach($sales_detail as $item){
            if(isset($item->delete_) && $item->delete_){
                $sales_detail_item = \Sales\Model\SalesDetail::find($item->id);
                if(!empty($sales_detail_item)){
                    $update = true;
                    $sales_detail_item->delete();
                    $sales_detail_item->uniqid = $item->uniqid;
                    $sales_detail_items[] = $sales_detail_item;
                }
            } else if(isset($item->create_) && $item->create_){
                $update = true;
                $sales_detail_item = \Sales\Model\SalesDetail::create((array)$item);
                $sales_detail_item->uniqid = $item->uniqid;
                $sales_detail_items[] = $sales_detail_item;
            } else if(isset($item->replace_) && $item->replace_){
                $update = true;
                \Sales\Model\SalesDetail::destroy($item->id);
                $sales_detail_item = \Sales\Model\SalesDetail::create((array)$item);
                $sales_detail_item->uniqid = $item->uniqid;
                $sales_detail_items[] = $sales_detail_item;
            } else if(isset($item->update_) && $item->update_){
                $update = true;
                $sales_detail_item = \Sales\Model\SalesDetail::find($item->id);
                $sales_detail_item->update((array)$item);
                $sales_detail_item->uniqid = $item->uniqid;
                if($sales_detail_item->wasChanged('count') && $reserv->check_reserv($sales_detail_item->id, "sales", 1)){
                    $count = \Sales\Support\SalesSupport::get_count_realization_left($sales_detail_item->id);
                    $reserv->set_reserv($sales_detail_item->id, "sales", 1, $count);
                    $sales_detail_item->update_ndr = true;
                }
                $sales_detail_items[] = $sales_detail_item;
            } else {
                $sales_detail_items[] = $item;
            }
        }

        if($update){
            \Sales\Support\SalesSupport::re_total($sales_id);
            $sync = new \Sync\Sync;
            $sync->syncRegister('sales', $sales_id, '2', $sales->org);
        }

        return response(["status" => true, "sales_detail" => $sales_detail_items]);
    }

    static function search(){
        $SearchName = new \Sales\SearchName;
        display("@".static::$namespace."/manager/search", [
            "template" => $SearchName->view_re(),
        ]);
    }

    static function get_search(Request $request){
        $SearchName = new \Sales\SearchName;
        $SearchName->search($request->all());
    }

    static function print($type, $id, Request $request){
        \Sales\Support\SalesPrintSupport::print_sales($id, $type, $request->all());
    }

    static function redirect_original($sales_id){
        $sales = \Sales\Model\Sales::where("clone", $sales_id)->first();
        return redirect()->route('admin.sales.show', ["id" => $sales->id]);
    }
}