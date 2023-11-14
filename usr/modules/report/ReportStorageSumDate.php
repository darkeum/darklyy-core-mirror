<?php

namespace Report;

use Catalog\Models\Catalog;
use Boot\System\Http\Request;


class ReportStorageSumDate extends Report
{
    static function getTitle()
    {
        return "Отчёт по сумме на складах на определенную дату";
    }

    static function getReportName()
    {
        return "storage_sum_date";
    }

    static function getDescription()
    {
        return "Отчёт по сумме на складах (текущая закупочная цена) на определенную дату";
    }

    static function fields()
    {
        return [
            ["value" => "st.stock_id", "text" => "Склад", "modal" => \Report\Modal\StockModal::class,],
            ["value" => "st.date", "text" => "Дата", "modal" => \Report\Modal\DateModal::class,],
        ];
    }

    static function query()
    {
        return \DB::table("stock_transaction as st")->select('cid', 'date', 'sum', 'stock_id');
    }


    static function get_data(Request $request)
    {
        $selections = static::get_selections($request);

        $query = static::query();

        foreach ($selections as $selection) {
            $query = static::condition_sql($query, $selection);
        }

        return $query->get();
    }


    static function table_data(Request $request, $config = [])
    {
        $data = static::get_data($request);
        $table = static::get_table_structure();
        $date = date('Y-m-d');
        $selections = static::get_selections($request);
        foreach ($selections as $selection) {
            if ($selection->field == 'st.date') {
                $date = $selection->value;
            }
        }

        $table->head = [
            static::element("ID склада/товара"),
            static::element("Наименование склада/товара"),
            static::element("Кол-во"),
            static::element("Закуп"),
            static::element("Сумма"),
        ];


        $data_prepare = [];
        foreach ($data as $item) {
            if (isset($data_prepare[$item->stock_id][$item->cid])) {
                if ($data_prepare[$item->stock_id][$item->cid]['date'] <= $item->date) {
                    $data_prepare[$item->stock_id][$item->cid]['date'] = $item->date;
                    $data_prepare[$item->stock_id][$item->cid]['sum'] = $item->sum;
                }
            } else {
                $data_prepare[$item->stock_id][$item->cid] = [
                    'cid' => $item->cid,
                    'c_title' => $item->cid,
                    'sum' => $item->sum,
                    'date' => $item->date
                ];
                if ($item->sum > 0) {
                }
            }
        }
        $i = 0;
        $storages = [];
        foreach ($data_prepare as $key_storage => $value_storage) {



            $storages[$key_storage] = cc();
            $storages[$key_storage]->stock_id = $key_storage;
            $storages[$key_storage]->name = 'Склад';
            $storages[$key_storage]->sum = 0;
            foreach ($value_storage as $value_item) {
                $i++;
                if ($i < 10000) {
                    $cid[$key_storage][$value_item['cid']] = cc();
                    $cid[$key_storage][$value_item['cid']]->cid = $value_item['cid'];
                    $cid[$key_storage][$value_item['cid']]->name = $value_item['cid'];
                    $cid[$key_storage][$value_item['cid']]->price = 0;
                    $cid[$key_storage][$value_item['cid']]->total = 0;
                    $cid[$key_storage][$value_item['cid']]->count = $value_item['sum'];
                }
                if ($value_item['sum'] > 0) {
                    $price = \Price::getOriginalPurchaseByDate($value_item['cid'], $date);
                    if (!empty($price->data->sum)) {
                        $storages[$key_storage]->sum += $value_item['sum'] * $price->data->sum;
                        if ($i < 10000) {
                            $cid[$key_storage][$value_item['cid']]->price = $price->data->sum;
                            $cid[$key_storage][$value_item['cid']]->total = $value_item['sum'] * $price->data->sum;
                            $ct = Catalog::find($value_item['cid']);
                            $cid[$key_storage][$value_item['cid']]->name = $ct->title ?? '';
                        }
                    } else {
                        if ($i < 10000) {
                            $cid[$key_storage][$value_item['cid']]->price = 0;
                            $cid[$key_storage][$value_item['cid']]->total = 0;
                            $cid[$key_storage][$value_item['cid']]->name = $value_item['cid'];
                        }
                    }
                }
            }
        }

        foreach ($storages as $storage) {
            $table->body[] = [
                static::element($storage->stock_id),
                static::element($storage->name),
                static::element(""),
                static::element(""),
                static::element($storage->sum, ["round" => true]),
            ];
            foreach ($cid[$storage->stock_id] as $cid_value) {
                $table->body[] = [
                    static::element($cid_value->cid),
                    static::element($cid_value->name),
                    static::element($cid_value->count),
                    static::element($cid_value->price),
                    static::element($cid_value->total, ["round" => true]),
                ];
            }
        }



        return $table;
    }
}
