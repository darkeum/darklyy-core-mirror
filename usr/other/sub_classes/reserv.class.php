<?php

use Carbon\Carbon;
use Boot\Models\StockReserv;
use Boot\System\Cache\Cache;
use Boot\Support\Facades\Auth;

class Reserv
{
    private $uid;

    public $count = 0;
    public $future_count = 0;

    function __construct(){
        $this->data = cc();
        $this->uid = Auth::id();
    }

    public function change_uid($uid){
        $this->uid = $uid;
    }

    // public function query_stock_reserv_custom($request){
    //     global $db;

    //     $pid = $request["pid"];
    //     $module = $request["module"];
    //     $stock_id = $request["stock_id"];
    //     $status = $request["status"] ?? null;
    //     $custom_query = $request["custom_query"] ?? null;
    //     $field = $request["field"] ?? "*";

    //     $status_query = "status != '0'";
    //     if(!is_null($status)) $status_query = "status IN(".$status.")";
    //     return $db->query("SELECT {$field} FROM `".DB_PREFIX."_stock_reserv` WHERE `module` = '{$module}' AND `pid` = '{$pid}' AND `stock_id` = '{$stock_id}' AND ".$status_query." ".$custom_query."");
    // }

    // public function query_stock_reserv($pid, $module, $stock_id, $status = null, $custom_query = null){
    //     return $this->query_stock_reserv_custom([
    //         "pid" => $pid,
    //         "module" => $module,
    //         "stock_id" => $stock_id,
    //         "status" => $status,
    //         "custom_query" => $custom_query,
    //     ]);
    // }

    public function get_reserv_count($pid, $module, $stock_id, Array $status){
        return StockReserv::where("pid", $pid)->
        where("module", $module)->
        where("stock_id", $stock_id)->
        whereIn("status", $status)->
        sum("count");
    }

    public function get_reserv_quantity($cid, $stock_id, Array $status = [1]){
        return StockReserv::where("cid", $cid)->
        where("stock_id", $stock_id)->
        whereIn("status", $status)->
        sum("count");
    }

    public function check_reserv($pid, $module, $stock_id){
        return StockReserv::where("pid", $pid)->
        where("module", $module)->
        where("stock_id", $stock_id)->
        exists();
    }

    public function check_reserv_uid($pid, $module, $stock_id){
        $item = StockReserv::where("pid", $pid)->
        where("module", $module)->
        where("stock_id", $stock_id)->
        where("uid", $this->uid)->
        first();
        if(!empty($item)){
            if($item->uid == $this->uid){
                return true;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    // public function check_reserv_count($pid, $module, $stock_id, $count, $uid = false){
    //     global $db;
    //     if(empty($pid)){
    //         return false;
    //     } else {
    //         $status = 1;
    //         if($uid){
    //             $custom_query = "AND uid = '".$this->uid."'";
    //         } else {
    //             $custom_query = null;
    //         }
    //         $query = $this->query_stock_reserv($pid, $module, $stock_id, $status, $custom_query);
    //         $sum = 0;
    //         if($db->numRows($query) > 0){
    //             while($r = $db->getRow($query)){
    //                 $sum += $r["count"];
    //             }
    //             if($count <= $sum){
    //                 return true;
    //             } else {
    //                 return false;
    //             }
    //         } else {
    //             return false;
    //         }
    //     }
    // }

    public function set_reserv($pid, $module, $stock_id, $count = null){
        //проверить на количество резерва и продажи
        $this->data = $this->get_module($pid, $module);
        $count = is_null($count) ? $this->data->count : $count;
        $this->data->count = $count;
        $cid = $this->data->cid;

        $items = StockReserv::where("pid", $pid)->
        where("module", $module)->
        where("stock_id", $stock_id)->
        get();

        if(count($items) > 0){
            $reserv_count = 0;
            foreach($items as $item){
                $reserv_count += $item->count;
            }
            if($count < $reserv_count){
                //если количество продажи меньше резерва -> уменьшить резерв
                $this->del_reserv($pid, $module, stock_id:$stock_id);
            } else {
                /* (1) */
                //иначе проверить на количество доступного
                //если количество достаточно -> создать резерв
                //иначе максимальное количество доступного -> создать резерв, остальное -> установить на будущий резерв
                $count -= $reserv_count;
            }
        }
        $this->check_and_create($cid, $stock_id, $module, $pid, $count);
    }

    //создание резерва с учетом ндр(для создания будущего резерва)
    private function check_and_create($cid, $stock_id, $module, $pid, $count){
        $ndr = get_ndr(["cid" => $cid, "quantity" => "",  "stock_id" => $stock_id, "module" => $module, "pid" => $pid, "returnType" => "array"]);
        if($ndr["d"] >= $count){
            $this->create_reserv($pid, $module, $stock_id, $count, $cid);
            $this->count = $count;
        } else {
            if($ndr["d"] > 0){
                $this->create_reserv($pid, $module, $stock_id, $ndr["d"], $cid);
                $this->count = $ndr["d"];
            }
            //будущий резерв
            $this->future_count = $count - $ndr["d"];
            $this->create_reserv($pid, $module, $stock_id, $this->future_count, $cid, 2);
        }
    }

    private function cache_reserv_delete($pid){
        $cache = new Cache;
        $cache->deleteItem("sales_reserv.{$pid}");
    }

    //создание резерва
    private function create_reserv($pid, $module, $stock_id, $count, $cid, $status = 1){
        if($count > 0){
            $reserv = StockReserv::create([
                "cid" => $cid, 
                "stock_id" => $stock_id, 
                "uid" => $this->uid, 
                "module" => $module, 
                "pid" => $pid, 
                "count" => $count, 
                "date" => Carbon::now(), 
                "status" => $status,
            ]);
            $this->cache_reserv_delete($pid);
        }
    }

    private function get_module($pid, $module){
        switch($module){
            default:
                $mod = $module;
            break;
            case "sales":
                $mod = "sales_detail";
            break;
        }
        return \DB::table($mod)->where("id", $pid)->first();
    }

    //удаление резерва
    public function del_reserv($pid, $module, Array $status = [], $stock_id = null){
        $items = StockReserv::where("pid", $pid)->
        where("module", $module);
        if(!empty($status)){
            $items->whereIn("status", $status);
        }
        if(!is_null($stock_id)){
            $items->where("stock_id", $stock_id);
        }
        $items->delete();
        $this->cache_reserv_delete($pid);
    }

    //когда товар списывается
    public function end_reserv($pid, $module, $stock_id, $count){
        $count_reserv = $this->get_reserv_count($pid, $module, $stock_id, ["1", "2"]);
        $this->del_reserv($pid, $module, stock_id:$stock_id);
        if($count_reserv > $count){
            $data = $this->get_module($pid, $module);
            $new_count = $count_reserv - $count;
            $this->check_and_create($data->cid, $stock_id, $module, $pid, $new_count);
        }
    }

    //при приходе товара, уменьшить будущий резерв
    public function confirm_reserv($pid, $module, $stock_id, $count, $cid){
        $count_reserv = $this->get_reserv_count($pid, $module, $stock_id, ["2"]);
        if($count_reserv > 0){
            //если есть будущий резерв ->
            //получить сумму будущего резерва

            if($count >= $count_reserv){
                //если поступление >= будущему резерву -> удалить весь будущий резерв и создать резерв
                $this->del_reserv($pid, $module, [2], $stock_id);
                $this->create_reserv($pid, $module, $stock_id, $count_reserv, $cid);
            } else {
                //иначе удалить весь будущий резерв
                $this->del_reserv($pid, $module, [2], $stock_id);
                //создать основной резерв с количеством поступившего
                $this->create_reserv($pid, $module, $stock_id, $count, $cid);
                //создать будущий резерв с оставшимся количеством
                $after_count = $count_reserv - $count;
                $this->create_reserv($pid, $module, $stock_id, $after_count, $cid, 2);
            }
        }
    }
}
