<?php

namespace Storage;

class SearchName extends \SearchName\SearchName
{
    protected $url = "/administration/module/storage/search_name";

    public function search($request)
    {
        if (empty($request["search"])) {
            echo "<div>Поиск не указан!</div>";
        } else {
            $search = $request["search"];

            $data = \Storage\Models\StockTransferDetail::where("s_name", "like", "%{$search}%")->orWhere("s_sku", "{$search}")->has("stock_transfer");
            echo '<div><h3>Перемещения</h3><ul>';
            if ($data->count() > 0) {
                foreach ($data->get() as $item) {
                    if (isset($item->stock_transfer->active)) {
                        $active = (($item->stock_transfer->active ?? 0) == 0) ? "text-danger" : "text-success";
                        echo "<li><a target='_blank' href='/administration/module/storage/transfer/detail/{$item->transfer_id}' class='{$active}'>Перемещение № " . ($item->stock_transfer->numb ?? null) . "</a></li>";
                    }
                }
                echo '</ul></div>';
            } else {
                echo "<div>Перемещения отсутствуют</div>";
            }

            $data = \DB::table('storage_assembly_detail as sad')
                ->leftJoin("storage_assembly as sa", "sa.id", "=", "sad.said")
                ->leftJoin("sales_detail as sd", "sd.id", "=", "sad.sdid")
                ->where("sd.s_name", "{$search}")->orWhere("sd.s_sku", "{$search}")
                ->select('sad.*', 'sa.status as sa_status');
            // dd($data->toSql());
            echo '<div><h3>Сборки</h3><ul>';
            if ($data->count() > 0) {
                foreach ($data->get() as $item) {
                    $active = $item->sa_status == 1 ? "text-danger" : "text-success";
                    echo "<li><a target='_blank' href='/administration/module/storage/assembly/detail/{$item->said}' class='{$active}'>Сборка № {$item->said}</a></li>";
                }
                echo '</ul></div>';
            } else {
                echo "<div>Сборки отсутствуют</div>";
            }
        }
    }
}
