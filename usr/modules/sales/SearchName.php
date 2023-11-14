<?php

namespace Sales;

class SearchName extends \SearchName\SearchName{
    
    protected $url = "/administration/module/sales/search_name";

    public function search($request){
        $search = $request["search"];

        $items = \Sales\Model\SalesDetail::with(["sales", "sales.organization", "sales.object_"])->
        orWhere("s_name", "LIKE", "%{$search}%")->
        orWhere("s_sku", "LIKE", "%{$search}%")->
        get();

        if(count($items) > 0){
            foreach($items as $item){
                echo '<li style="margin-bottom: 20px;">
                    <a style="color: black;" target="_blank" href="/administration/module/sales/order/'.$item->sales_id.'">
                        <b>Продажа № '.$item->sales->numb.';</b><br>
                        Ответственный: '.shortUserName($item->sales->uid).';<br>
                        Дата: '.(date("d.m.Y", strtotime($item->sales->date))).';<br>
                        Контрагент: '.(empty($item->sales->organization->name) ? 'Не указан' : $item->sales->organization->name).';<br>
                        Объект: '.(empty($item->sales->object_->name) ? 'Не указан' : $item->sales->object_->name).';<br>
                    </a>
                </li>';
            }
        } else {
            echo 'Продажи отсутствуют';
        }
    }
}