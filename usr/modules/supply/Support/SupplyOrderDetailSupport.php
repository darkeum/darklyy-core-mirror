<?php

namespace Supply\Support;

class SupplyOrderDetailSupport{
    static public function detail_query($id){
        global $db;
	    $rsql = static::receipts_sql();
	    $query = $db->query("SELECT so.supply_o, so.supply_d, so.nds, so.type_nds, so.total as sototal, so.price_now, so.id as soid, so.count, 
	    rd.count as count_receipts, so.comment, so.sale_detail_id, so.s_name, so.s_sku, c.*, so.unit as so_unit, so.date_ship, so.date_end, so.cid 
	    FROM `".DB_PREFIX."_supply_order_detail` as so 
	    LEFT JOIN `".DB_PREFIX."_catalog` as c ON(so.cid = c.id) 
	    LEFT JOIN (
	    	{$rsql}
	    ) as rd ON(rd.supply_detail_id = so.id) 
	    WHERE so.supply_o = '{$id}' AND status != 0 AND so.deleted_at IS NULL");
	    return $query;
    }

    static public function receipts_sql(){
        return "SELECT rd.supply_detail_id, IFNULL(SUM(count), 0) as count
		FROM `".\DB::getTablePrefix()."receipts_detail` as rd
		LEFT JOIN `".\DB::getTablePrefix()."receipts` as r ON(r.id = rd.rid)
		WHERE r.active = '1' 
		GROUP BY rd.supply_detail_id";
    }

    static function re_total_nds($supply_o){
        $update = [
            "total" => 0,
            "nds" => 0,
        ];
        $items = \Supply\Model\SupplyOrderDetail::where("supply_o", $supply_o)->get();
        foreach($items as $item){
            $update["total"] += $item->total;
            $update["nds"] += $item->nds;
        }
        \Supply\Model\SupplyOrder::find($supply_o)->update($update);
    }

    static function c_delete($id){
        $result = ["error" => []];
        $id_ = is_array($id) ? $id : [$id];
        $data = null;
        foreach($id_ as $i){
            $data = \Supply\Model\SupplyOrderDetail::find($i);
            try{
                if(!empty($data->supply_d)){
                    $data2 = \Supply\Model\SupplyDetail::find($data->supply_d)->toArray();
                    if(!empty($data2["sid"])){
                        \Supply\Model\Supply::find($data2["sid"])->update(["status" => 1]);
                    }
                }
                static::check_delete($i);
                $data->delete();
                \Logistics\Support\LogisticsDetailSupport::delete_supply_order($i);
            } catch(\Exception $e){
                $result["error"][] = $e->getMessage();
            }
        }
        if(!is_null($data)) static::re_total_nds($data["supply_o"]);
        return $result;
    }

    static function check_delete($id){
        global $db;
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_receipts_detail` WHERE supply_detail_id = '{$id}' LIMIT 1");
        if($db->numRows($query) > 0){
            $r = $db->getRow($query);
            throw new \Exception("Удалите все поступления товара {$r['name']}, прежде чем удалять!");
        }

        $query = $db->query("SELECT cd.* 
        FROM `".DB_PREFIX."_control_detail` as cd 
        LEFT JOIN `".DB_PREFIX."_control` as c ON(c.id = cd.control_id) 
        WHERE cd.sod_id = '{$id}' AND c.status = '1' LIMIT 1");
        if($db->numRows($query) > 0){
            $r = $db->getRow($query);
            throw new \Exception("Товар {$r['s_name']} отправлен на согласование!");
        }
    }
}