<?php

namespace Realization\Support;

class CorrectDetailSupport{
    static function check_delete($id){
        global $db;
        $data = [
            "error" => false,
        ];
        try{
            $query = $db->query("SELECT c.numb 
            FROM `".DB_PREFIX."_correct_detail` as cd
            LEFT JOIN `".DB_PREFIX."_correct` as c ON(cd.correct_id = c.id)
            WHERE cd.pid_detail = '{$id}' AND c.module = 'correct' LIMIT 1");

            if($db->numRows($query) > 0){
                $r = $db->getRow($query);
                $numb = $r["numb"];
                throw new \Exception("Имеется корректировка корректировки. Удалите связанные позиции. (Корректировка №{$numb})");
            }

            $query = $db->query("SELECT c.active 
            FROM `".DB_PREFIX."_correct_detail` as cd
            LEFT JOIN `".DB_PREFIX."_correct` as c ON(cd.correct_id = c.id)
            WHERE cd.id = '{$id}' LIMIT 1");
            $r = $db->getRow($query);

            if($r["active"] == 1){
                throw new \Exception("Корректировка проведена. Распроведите корректировку для удаления!");
            }
        } catch(\Exception $e){
            $data["error"] = $e->getMessage();
        }

        return $data;
    }

    static function getCorrectDetailQuery($id){
        global $db;
        $query = $db->query("SELECT cd.*, rd.s_name, rd.s_sku, rd.s_unit, rd.cid, 
            rd.count as rd_count, cd2.count as cd2_count,
            rd.s_price as rd_s_price, cd2.s_price as cd2_s_price, 
            rd.discount as rd_discount, cd2.discount as cd2_discount, 
            rd.total as rd_total, cd2.total as cd2_total,
            rd.nds_type as rd_nds_type, cd2.nds_type as cd2_nds_type, 
            rd.nds as rd_nds, cd2.nds as cd2_nds, 
            rd.discount_sum as rd_discount_sum, cd2.discount_sum as cd2_discount_sum 
            FROM `".DB_PREFIX."_correct_detail` as cd 
            LEFT JOIN `".DB_PREFIX."_correct_detail` as cd2 ON(cd2.id = cd.pid_detail)
            LEFT JOIN `".DB_PREFIX."_realization_detail` as rd ON(rd.id = cd.realization_detail_id)
            WHERE cd.correct_id = '".$id."'");

        return $query;
    }  
    
    static function getCorrectDetail($id){
        global $db;
        $query = static::getCorrectDetailQuery($id);
        $flagCorrect = \Realization\Model\Correct::checkCorrectMod($id);
        $result = array();
        if($db->numRows($query) > 0) {          
            while($r = $db->getRow($query)){
                $result[] = array(
                    'unit' => get_unit('data', $r['s_unit'] ,'sname'),
                    's_unit' => $r['s_unit'],
                    'cid' => $r["cid"],
                    'ndr_view' => get_ndr(["cid" => $r["cid"], "stock_id" => "1", "module" => "sales", "pid" => "", "returnType" => "view"]),
                    'typeNds' => \Price::getNds($r["nds_type"]),   
                    'count' => $flagCorrect ? $r["cd2_count"] : $r["rd_count"],
                    'price' => $flagCorrect ? $r["cd2_s_price"] : $r["rd_s_price"],
                    'discount' => $flagCorrect ? $r["cd2_discount"] : $r["rd_discount"],
                    'total' => $flagCorrect ? $r["cd2_total"] : $r["rd_total"],
                    'discount_sum' => $flagCorrect ? $r["cd2_discount_sum"] : $r["rd_discount_sum"],
                    'nds_type' => $flagCorrect ? \Price::getNds($r["cd2_nds_type"]) : \Price::getNds($r["rd_nds_type"]),
                    'nds' => $flagCorrect ? $r["cd2_nds"] : $r["rd_nds"],
                );
            }
        }

        return $result;
    }
}