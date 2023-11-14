<?php

namespace Control\Controller;

use \Boot\Controller\AbstractController;
use Boot\Models\Attach;
use Boot\Models\CommentsAdmin;
use \Boot\System\Http\Request;
use \Control\Grid\ControlGrid;
use Control\Model\Control;
use Control\Model\ControlDetail;

class ControlController extends AbstractController{
    static function index(){
        $grid = new ControlGrid;
        display('@control/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }

    // static function create(){

    // }

    // static function store(Request $request){

    // }

    private static function getControlData($id){
        $control = Control::with(["supply_order", "control_detail.supply_order_detail", "control_detail.sales_detail.sales.object_"])->where("id", $id)->first();
        $supply_order_total = 0;
        $sales_total = null;
        foreach($control->control_detail as $key => $item){
            $control->control_detail[ $key ]->ndr = get_ndr(["cid" => $item->cid, "stock_id" => "1", "returnType" => "view"]);

            $supply_order_total += $item->supply_order_detail->total ?? 0;
            if(!empty($item->sales_detail)){
                $sales_total += $item->sales_detail->total;
            }
        }
        return [
            "control" => $control,
            "supply_order_total" => $supply_order_total,
            "sales_total" => $sales_total,
        ];
    }

    private static function getSalesData($control){
        $sales = cc();
        foreach($control->control_detail as $key => $item){
            if(!empty($item->sales_detail) && !$sales->contains("id", $item->sales_detail->sales->id)) $sales->push($item->sales_detail->sales);
        }
        return $sales;
    }

    private static function getComments($control){
        return CommentsAdmin::with("user")->where("module", "supply_order_control")->where("module_id", $control->supply_o)->get();
    }

    private static function getAttach($control){
        return Attach::where("module", "supply")->where("pid", $control->supply_o)->get();
    }

    static function get_buttons_path(){
        return "@control/manager/show/control_buttons";
    }

    static function show($id, Request $request){
        $data = static::getControlData($id);
        $control = $data["control"];
        $temp_data = [
            "id" => $id,
            "control" => $control,
            "comments" => static::getComments($control),
            "sales" => static::getSalesData($control),
            "attach" => static::getAttach($control),
            "supply_order_total" => $data["supply_order_total"],
            "sales_total" => $data["sales_total"],
            "buttons_path" => static::get_buttons_path(),
        ];
        switch($request->get("view")){
            default:
                display('@control/manager/show/show', $temp_data);
            break;
            case "head":
                display('@control/manager/show/show_head', $temp_data);
            break;
            case "body":
                display('@control/manager/show/show_body', $temp_data);
            break;
        }
        
    }

    static function edit($id){
        
    }

    static function update(Request $request, $id){
        $control = Control::findOrFail($id);
        $control->update($request->all());
        echo $control->toJson();
    }

    static function destroy($id){
        
    }

    static function end($id, Request $request){
        $status = $request->get("status");
        $comment = $request->get("comment");
        if(isset($id, $status)){
            return response(\Control\Support\Control::end($id, $status, $comment));
        }
    }
}