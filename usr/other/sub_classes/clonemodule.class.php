<?php

class CloneModule{
    protected $parent_class;
    protected $children_class;
    protected $external_field;
    public $parent_id;
    public $children_id;
    public $clone_parent_id;
    public $clone_children_id;

    public function __construct($request){
        $this->parent_id = empty($request["parent_id"]) ? false : $request["parent_id"];
        $this->children_id = empty($request["children_id"]) ? false : $request["children_id"];
        if($this->children_id){
            $children_data = $this->children_class::withTrashed()->where("id", $this->children_id)->first();
            $this->clone_children_id = $children_data->clone;
        }
        if($this->children_id && !$this->parent_id){
            $this->parent_id = $children_data->{$this->external_field};
        }
        if($this->parent_id){
            $parent_data = $this->parent_class::withTrashed()->where("id", $this->parent_id)->first();
            $this->clone_parent_id = $parent_data->clone;
        }
    }

    public function check_clone(){
        $data = $this->parent_class::findOrFail($this->parent_id);
        return !($data->clone_active == 0);
    }

    public function check_children(){
        $data = $this->parent_class::where("clone_active", 1)->where("clone", $this->parent_id)->first();
        if(empty($data)){
            return false;
        } else {
            return [
                "id" => $data->id,
            ];
        }
    }

    public function add_children($request){
        if($this->check_clone()){
            $parent_data = $this->parent_class::findOrFail($this->parent_id);
            $request[ $this->external_field ] = $parent_data->clone;
            $children = $this->children_class::create($request);
            $iid = $children->id;
            $this->children_class::findOrFail($this->children_id)->update(["clone" => $iid]);
            return $iid;
        }
    }

    public function update_parent($request){
        if($this->check_clone() && !empty($this->clone_parent_id)){
            $this->parent_class::findOrFail($this->clone_parent_id)->update($request);
        }
    }

    public function update_children($request){
        if($this->check_clone() && !empty($this->clone_children_id)){
            $this->children_class::findOrFail($this->clone_children_id)->update($request);
            return true;
        }
    }

    public function delete_children(){
        if($this->check_clone() && !empty($this->clone_children_id)){
            $this->children_class::destroy($this->clone_children_id);
        }
    }
}