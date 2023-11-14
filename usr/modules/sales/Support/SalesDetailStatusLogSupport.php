<?php

namespace Sales\Support;

class SalesDetailStatusLogSupport{
    static function get_data($id){
        $data = [];
        $status = \Sales\Model\SalesDetail::getStatus();
        foreach(\Sales\Model\SalesDetailStatusLog::where("sales_detail_id", $id)->get()->toArray() as $item){
            $s = $status[ $item["status"] ];
            $element = new \stdClass;
            $element->url = $item["url"];
            $element->text = $s["title"].'; '.shortUserName($item["uid"]).'; '.(date("d.m.Y H:i:s", strtotime($item["updated_at"])));
            $element->status_span = $s["span"];
            $data[] = $element;
        }
        return $data;
    }

    static function get_view($id){
        $data = static::get_data($id);
        if(empty($data)){
            return 'Лог отсутствует';
        } else {
            $result = [];
            foreach($data as $item){
                $result[] = <<<HTML
                    <div class="mb5">{$item->status_span}<a href="{$item->url}" target="_blank" class="ml10">{$item->text}</a></div>
HTML;
            }
            return implode("", $result);
        }
    }
}