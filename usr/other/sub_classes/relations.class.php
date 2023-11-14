<?php

use \Boot\Models\Relations as Rel;
use \Carbon\Carbon;

class Relations{
    public function __construct(){
        $this->uid = \Boot\Support\Facades\Auth::id();
    }
    private function param_to_search_el($param, $allow_key){
        $search = [];
        if(count($param) > 0){
            foreach($param as $key => $val){
                if(in_array($key, $allow_key)) $search[] = [$key, "=", $val];
            }
        }
        return $search;
    }
    public function add($param){
        if(empty($param["module_in"])) throw new \Exception("Empty field `module_in`");
        if(empty($param["pid_in"])) throw new \Exception("Empty field `pid_in`");
        if(empty($param["module_out"])) throw new \Exception("Empty field `module_out`");
        if(empty($param["pid_out"])) throw new \Exception("Empty field `pid_out`");

        $rel = new Rel;
        $rel->module_in = $param["module_in"];
        $rel->pid_in = $param["pid_in"];
        $rel->module_out = $param["module_out"];
        $rel->pid_out = $param["pid_out"];
        $rel->save();
        return $rel->id;
    }
    public function delete($param){
        $allow_key = ["module_in", "pid_in", "module_out", "pid_out"];
        $result = [];
        $search = $this->param_to_search_el($param, $allow_key);
        if(count($search) > 0){
            $result = Rel::where($search)->get();
            Rel::where($search)->delete();
        }
        return $result;
    }
    public function find($param){
        $result = [];
        $allow_key = ["module_in", "pid_in", "module_out", "pid_out"];
        $search = $this->param_to_search_el($param, $allow_key);
        if(count($search) > 0){
            $items = Rel::where($search)->get();
            foreach($items as $item){
                $result[] = $item->toArray();
            }
        }
        return $result;
    }
}