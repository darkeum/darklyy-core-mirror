<?php

namespace Supply;

class AutomaticReceipts{
    protected $relation_name = "automatic_receipts";
    protected $relation_realization_name = "realization";
    protected $relation_receipts_name = "receipts";

    protected $receipt_id;
    protected $sales_detail_data;
    protected $supply_order_id;
    protected $org;
    protected $stock_id;
    protected $object;
    protected $nds_includes;
    protected $active;
    protected $sales_to_realization;
    protected $supply_order_detail_data;
    
    public function __construct($receipt_id){
        $this->receipt_id = $receipt_id;

        $this->sales_detail();
    }

    public function sales_detail(){
        global $db;
        $result = [];
        $org = "";
        $stock_id = "";
        $query = $db->query("SELECT rd.count as rd_count, rd.price as rd_price, rd.type_nds as rd_type_nds,
        r.active as r_active, r.nds_includes as r_nds_includes,
        s.stock_id as s_stock_id, s.org as s_org, s.object as s_object,
        sd.id as c_id, sd.sales_id as c_sales_id,
        sd2.id as p_id, sd2.sales_id as p_sales_id, sd2.cid as p_cid, sd2.s_sku as p_s_sku, sd2.s_name as p_s_name, sd2.s_unit as p_unit
        FROM `".DB_PREFIX."_receipts_detail` as rd
        LEFT JOIN `".DB_PREFIX."_receipts` as r ON(r.id = rd.rid)
        LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(rd.sales_detail_id = sd.id)
        LEFT JOIN `".DB_PREFIX."_sales_detail` as sd2 ON(sd.id = sd2.clone)
        LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd2.sales_id)
        WHERE rd.rid = '".$this->receipt_id."' AND rd.sales_detail_id IS NOT NULL AND s.clone_active = 1");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $result[] = $r;

                $org = $r["s_org"];
                $stock_id = $r["s_stock_id"];
                $object = $r["s_object"];
                $active = $r["r_active"];
                $nds_includes = $r["r_nds_includes"];
            }

            $this->org = $org;
            $this->stock_id = $stock_id;
            $this->object = $object;
            $this->active = $active;
            $this->nds_includes = $nds_includes;
        }
        $this->sales_detail_data = $result;
    }

    public function automatic(){
        global $core;
        if($this->sales_detail_data){

            // $uid = $core->auth->user_info['id'];

            if($this->active == 1){
                $this->realization();

                $this->supply();
    
                $this->receipts();
            } else {
                $this->distribution();
            }
        }
    }

    private function get_sales_children_id(){
        $sales_detail_id_children = [];
        foreach($this->sales_detail_data as $item){
            $sales_detail_id_children[ $item["c_sales_id"] ][] = $item["c_id"];
        }
        return $sales_detail_id_children;
    }

    private function realization(){
        //создание проведенной реализации ребенка
        $module = new \Module;
        $relations = new \Relations;

        $sales_detail_id_children = $this->get_sales_children_id();
        $sales_to_realization = [];
        foreach($sales_detail_id_children as $sales_id => $sales_detail){
            $str = [];
            $str["sales_detail_id"] = $sales_detail;
            $str["active"] = 1;

            $rd = $module->sales_to_realization($sales_id, $str);
            $realization_id = $rd["realization_id"];
            $sales_to_realization[ $sales_id ] = $realization_id;
            $relations->add(["module_in" => $this->relation_name, "pid_in" => $this->receipt_id, "module_out" => $this->relation_realization_name, "pid_out" => $realization_id]);
        }
        $this->sales_to_realization = $sales_to_realization;
    }

    private function supply(){
        $this->supply_order();

        $this->supply_order_detail();
    }

    private function supply_order(){
        //создание счёта поставщика у родителя
        $supply_order_add = [];
        $supply_order_add["org"] = $this->org;
        $supply_order_add["oid"] = 507;
        $supply_order_add["object"] = $this->object;
        $supply_order_add["nds_includes"] = $this->nds_includes;
        $date = date("Y-m-d");
        $supply_order_add["date_work"] = $date;
        $supply_order_add["date_ship"] = $date;
        $supply_order_add["date_end"] = $date;
        $supply_order_id = \Supply\Model\SupplyOrder::create($supply_order_add);

        $this->supply_order_id = $supply_order_id;
    }

    private function supply_order_detail(){
        $total = 0;
        $nds = 0;

        $supply_order_detail_data = [];

        foreach($this->sales_detail_data as $item){
            $auto_price = $this->auto_price($item["rd_price"]);

            $p_data = [
                "price" => $auto_price,
                "count" => $item["rd_count"],
                "nds_includes" => $this->nds_includes,
                "nds_type" => $item["rd_type_nds"],
            ];
            $data = \Price::get_all($p_data);

            $supply_order_detail_add = [];
            $supply_order_detail_add["supply_o"] = $this->supply_order_id;
            $supply_order_detail_add["cid"] = $item["p_cid"];
            $supply_order_detail_add["s_sku"] = $item["p_s_sku"];
            $supply_order_detail_add["s_name"] = $item["p_s_name"];
            $supply_order_detail_add["count"] = $item["rd_count"];
            $supply_order_detail_add["price_now"] = $data["price"];
            $supply_order_detail_add["total"] = $data["total"];
            $supply_order_detail_add["type_nds"] = $data["nds_type"];
            $supply_order_detail_add["nds"] = $data["nds"];
            $supply_order_detail_add["unit"] = $item["p_unit"];
            $supply_order_detail_add["sale_detail_id"] = $item["p_id"];

            $total += $supply_order_detail_add["total"];
            $nds += $supply_order_detail_add["nds"];
        
            $supply_order_detail = \Supply\Model\SupplyOrderDetail::create($supply_order_detail_add);
            $sdid = $supply_order_detail->id;

            $supply_order_detail_data[ $item["c_sales_id"] ][] = $sdid;
        }

        $this->supply_order_detail_data = $supply_order_detail_data;

        \Supply\Model\SupplyOrder::find($this->supply_order_id)->update(["total" => $total, "nds" => $nds]);
    }

    private function auto_price($price){
        return $price + $price * 0.04;
    }

    private function receipts(){
        $module = new \Module;
        $relations = new \Relations;

        $realization_data = \Realization\Model\Realization::find($this->sales_to_realization)->toArray();
        $sales_to_realization_numb = [];
        foreach($realization_data as $item){
            $sales_to_realization_numb[ $item["sales_id"] ] = $item["numb"];
        }

        foreach($this->supply_order_detail_data as $c_sales_id => $supply_detail_id){
            $supply_to_receipts_request = [];
            $supply_to_receipts_request["doc_number"] = $sales_to_realization_numb[ $c_sales_id ];
            $supply_to_receipts_request["date_receipts"] = date("Y-m-d");
            $supply_to_receipts_request["org"] = $this->org;
            $supply_to_receipts_request["stock_id"] = $this->stock_id;
            $supply_to_receipts_request["supply_detail_id"] = $supply_detail_id;
            $supply_to_receipts_request["active"] = 1;
            $str = $module->supply_to_receipts($this->supply_order_id, $supply_to_receipts_request);
            $receipts_id = $str["receipts_id"];
            $relations->add(["module_in" => $this->relation_name, "pid_in" => $this->receipt_id, "module_out" => $this->relation_receipts_name, "pid_out" => $receipts_id]);
        }
    }

    private function distribution(){
        $relations = new \Relations;

        $rel = ["module_in" => $this->relation_name, "pid_in" => $this->receipt_id];
        $data = $relations->find($rel);

        if($data){
            foreach($data as $item){
                switch($item["module_out"]){
                    case $this->relation_realization_name:
                        $d = \Realization\Model\Realization::findOrFail($item["pid_out"])->toArray();
                        if($d["active"] == 1){
                            require_once('usr/modules/realization/admin/sub/realization_active.php');
                            realization_active($item["pid_out"], true);
                        }
                    break;
                    case $this->relation_receipts_name:
                        $d = \Supply\Model\Receipts::find($item["pid_out"])->toArray();
                        if($d["active"] == 1){
                            require_once('usr/modules/supply/admin/sub/receipts_active.php');
                            receipts_active($item["pid_out"], true);
                        }
                    break;
                }
            }
            $relations->delete($rel);
        }

    }
}