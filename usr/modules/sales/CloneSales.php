<?php

namespace Sales;

class CloneSales extends \CloneModule{
    protected $parent_class = \Sales\Model\Sales::class;
    protected $children_class = \Sales\Model\SalesDetail::class;
    protected $external_field = "sales_id";

    public function add_children($request){
        $iid = parent::add_children($request);
        if($iid){
            \Sales\Support\SalesDetailSupport::recount_sales($this->clone_parent_id);
            return $iid;
        }
    }

    public function update_children($request){
        if(isset($request["clone"])){
            unset($request["clone"]);
        }
        $flag = parent::update_children($request);
        if($flag){
            \Sales\Support\SalesDetailSupport::recount_sales($this->clone_parent_id);
        }
    }

    public function add_reserv($request){
        if($this->check_clone()){
            $id = $this->clone_children_id;
            $data = $this->parent_class::find($this->clone_parent_id)->toArray();
            $stock_id = $data["stock_id"];
            $count = $request["count"];
            $reserv = new \Reserv;
            $reserv->set_reserv($id, "sales", $stock_id, $count);
        }
    }

    public function delete_reserv(){
        if($this->check_clone()){
            $id = $this->clone_children_id;
            $reserv = new \Reserv;
            $reserv->del_reserv($id, "sales");
        }
    }

    public function replace_children($old_id, $new_id){
        //old_id, new_id -> parent
        if($this->check_clone()){
            $old_data = $this->children_class::withTrashed()->where("id", $old_id)->first();
            $old_clone_data = $this->children_class::withTrashed()->where("id", $old_data->clone)->first();
            $new_data = $this->children_class::withTrashed()->where("id", $new_id)->first();

            $this->children_class::withTrashed()->where("id", $new_data->clone)->first()->update(["supply_detail_id" => $old_clone_data->supply_detail_id]);
        }
    }

    public function check_can_delete(){
        if($this->check_clone()){
            \Sales\Model\SalesDetail::check_can_delete($this->clone_children_id);
        }
    }
}