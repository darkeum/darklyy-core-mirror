<?php

namespace Report;

use Boot\System\Http\Request;



class ReportAverageDelivery extends Report{
    static function getTitle(){
        return "Отчёт по средней времени доставки";
    }
    
    static function getReportName(){
        return "average_delivery";
    }

    static function getDescription(){
        return "Отчёт по средней времени доставки";
    }

    static function fields(){
        return [
            ["value" => "r.stock_id", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
            ["value" => "c.id", "text" => "Номенклатура", "modal" => \Report\Modal\GoodsModal::class,],
        ];
    }

    static function additional_terms(){
        return <<<HTML
            <div class="row mt-3">
                <div class="col-2">
                    <label>
                        <input type="radio" name="typeCounting" value="1" checked>
                        По умолчанию
                    </label>
                </div>
                <div class="col-10">
                    <label>
                        <input type="radio" name="typeCounting" value="2">
                        Берем все значения и выкидываем 10% минимальных и 10 % максимальных и из оставшихся считать средний срок
                    </label>
                </div>
            </div>
HTML;
    }

    static function query(){
        $date = "so.date_bill";
        $date2 = "r.date";
        return \DB::table("catalog as c")->
        leftJoin("supply_order_detail as sod", "c.id", "=", "sod.cid")->
        leftJoin("supply_order as so", "so.id", "=", "sod.supply_o")->
        leftJoin("receipts_detail as rd", "rd.supply_detail_id", "=", "sod.id")->
        leftJoin("receipts as r", "r.id", "=", "rd.rid")->
        whereNotNull($date)->
        whereNotNull($date2)->
        select("c.id", "c.sku", "c.title", \DB::raw("DATEDIFF(JMY_{$date2}, JMY_{$date}) as date"), "so.id as so_id", "r.id as r_id", "so.numb as so_numb", "r.numb as r_numb");
    }

    static function table_data(Request $request, $config = []){
        $data = static::get_data($request);
        $table = static::get_table_structure();
        $typeCounting = $request->request->get("typeCounting");

        $table->head = [
            static::element("ID товара"),
            static::element(""),
            static::element("Артикул товара"),
            static::element("Наименование товара"),
            static::element("Среднее время (дней)"),
        ];

        $result = [];
        $all_data = [];
        foreach($data as $item){
            $result[ $item->id ][] = $item;
            if(array_key_exists($item->id, $all_data)){
                $all_data[ $item->id ]->date += $item->date;
                $all_data[ $item->id ]->count += 1;
            } else {
                $all_data[ $item->id ] = cc();
                $all_data[ $item->id ]->date = $item->date;
                $all_data[ $item->id ]->count = 1;
                $all_data[ $item->id ]->id = $item->id;
                $all_data[ $item->id ]->sku = $item->sku;
                $all_data[ $item->id ]->title = $item->title;
            }
        }

        if($typeCounting == 2){
            foreach($result as $id => $items){
                $count = count($items);
                if($count >= 5){
                    $count_ = round($count * 0.1);
                    usort($items, function ($item1, $item2) {
                        return $item1->date <=> $item2->date;
                    });
                    $new_items = array_slice($items, $count_, -$count_);
                    $all_data[ $id ]->date = 0;
                    $all_data[ $id ]->count = 1;
                    foreach($new_items as $new_item){
                        $all_data[ $id ]->date += $new_item->date;
                        $all_data[ $id ]->count += 1;
                    }
                } else {
                    continue;
                }
            }
        }

        foreach($all_data as $id => $item){
            $date = $item->date / $item->count;
            $table->body[] = [
                static::element($item->id),
                static::element(""),
                static::element($item->sku),
                static::element($item->title),
                static::element(round($date, 1)),
            ];

            $p = 0;
            foreach($result[ $id ] as $v){
                $p++;
                $so_name = "Счет поставщика №{$v->so_numb}";
                $r_name = "Поступление №{$v->r_numb}";
                $table->body[] = [
                    static::element(""),
                    static::element($p),
                    static::element(
                        $config["excel"] 
                        ? $so_name
                        : "<a target=\"_blank\" href=\"/administration/module/supply/order_detail/{$v->so_id}\">{$so_name}</a>",
                    ),
                    static::element(
                        $config["excel"] 
                        ? $r_name
                        : "<a target=\"_blank\" href=\"/administration/module/supply/receipts_detail/{$v->r_id}\">{$r_name}</a>",
                    ),
                    static::element($v->date),
                ];
            }
            $table->body[] = [];
        }

        return $table;
    }
}