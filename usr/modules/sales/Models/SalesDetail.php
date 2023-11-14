<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sales\Models;

use Boot\Models\Model;
use Sales\Models\Sales;
use Boot\Traits\SoftDeletesDel;
use Supply\Models\SupplyDetail;
use Supply\Models\SupplyOrderDetail;
use Sales\Models\SalesDetailStatusLog;
use Storage\Models\StockTransferDetail;

class SalesDetail extends Model
{
    use SoftDeletesDel;

    protected $fillable = ["sales_id", "cid", "realization", "supply_detail_id", "s_sku", "s_name", "s_barcode", "price_type", "first_price", "s_price_local", "s_unit", "supply_id", "stock", "stock_count", "comment", "date_post", "position", "clone", "del", "count", "s_price", "count_realization", "count_assembly", "discount", "discount_sum", "type_nds", "nds", "total", "status", "status_log", "supply", "local_date_delivery", "status_client"];

    protected $appends = ['status_view'];

    protected $attributes = [
        'del' => 0,
        'status' => 1,
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            SalesDetailStatusLog::create([
                "sales_detail_id" => $model->id,
                "status" => 1,
                "url" => "/administration/module/sales/order/" . $model->sales_id,
            ]);

            $clone_sales = new \Sales\CloneSales(["parent_id" => $model->sales_id, "children_id" => $model->id]);
            $clone_sales->add_children($model->toArray());
        });

        static::updated(function ($model) {
            $clone_sales = new \Sales\CloneSales(["children_id" => $model->id]);
            $clone_sales->update_children($model->getChanges());
        });

        static::deleted(function ($model) {
            $reserv = new \Reserv();
            $reserv->del_reserv($model->id, 'sales');

            $clone_sales = new \Sales\CloneSales(["children_id" => $model->id]);
            $clone_sales->delete_children();
        });
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    static function getLocalDateDeliverySource()
    {
        return [
            ["index" => 0, "name" => "в наличии"],
            ["index" => 7, "name" => "1 неделя"],
            ["index" => 1, "name" => "1-2 недели"],
            ["index" => 2, "name" => "2-4 недели"],
            ["index" => 3, "name" => "4-6 недель"],
            ["index" => 4, "name" => "6-8 недель"],
            ["index" => 8, "name" => "8-10 недель"],
            ["index" => 5, "name" => "10-12 недель"],
            ["index" => 6, "name" => "12-14 недель"],
            ["index" => 9, "name" => "Под заказ"],
            ["index" => 10, "name" => "Уточняется"],
        ];
    }

    static public function getLocalDateDelivery()
    {
        $items = static::getLocalDateDeliverySource();
        $list = [];
        foreach ($items as $item) {
            $list[$item["index"]] = $item["name"];
        }
        return $list;
    }

    public function getLocalDateDeliveryViewAttribute()
    {
        return static::getLocalDateDelivery()[$this->local_date_delivery] ?? $this->local_date_delivery;
    }

    public function catalog()
    {
        return $this->belongsTo(\Catalog_v2\Model\Catalog::class, "cid");
    }

    static public function getStatus()
    {
        return [
            0 => [
                "title" => 'Отсутствует',
                "icon" => 'fas fa-ban text-danger',
                "text" => '',
                "span" => '<span class="fas fa-ban text-danger" data-toggle="tooltip" title="Отсутствует"></span>',
            ],
            1 => [
                "title" => 'Товар добавлен',
                "icon" => 'fas fa-plus-circle text-success',
                "text" => '',
                "span" => '<span class="fas fa-plus-circle text-success" data-toggle="tooltip" title="Товар добавлен"></span>',
            ],
            2 => [
                "title" => 'Получение сроков и цен',
                "icon" => 'text-danger fa-md font-weight-bold fw-bold',
                "text" => 'SOS',
                "span" => '<span class="text-danger fa-md font-weight-bold fw-bold" data-toggle="tooltip" title="Получение сроков и цен">SOS</span>',
            ],
            3 => [
                "title" => 'Сроки и цены получены',
                "icon" => 'text-success fa-md font-weight-bold fw-bold',
                "text" => 'SOS',
                "span" => '<span class="text-success fa-md font-weight-bold fw-bold" data-toggle="tooltip" title="Сроки и цены получены">SOS</span>',
            ],
            4 => [
                "title" => 'Отправлено на закупку',
                "icon" => 'fa fa-shopping-cart text-primary fa-md',
                "text" => '',
                "span" => '<span class="fa fa-shopping-cart text-primary fa-md" data-toggle="tooltip" title="Отправлено на закупку"></span>',
            ],
            5 => [
                "title" => 'Ожидает согласования',
                "icon" => 'fa fa-truck text-warning fa-md',
                "text" => '',
                "span" => '<span class="fa fa-truck text-warning fa-md" data-toggle="tooltip" title="Ожидает согласования"></span>',
            ],
            6 => [
                "title" => 'Ожидает оплаты',
                "icon" => 'fa fa-truck text-ws fa-md',
                "text" => '',
                "span" => '<span class="fa fa-truck text-ws fa-md" data-toggle="tooltip" title="Ожидает оплаты"></span>',
            ],
            7 => [
                "title" => 'Товары прибыли',
                "icon" => 'fas fa-boxes text-success fa-md',
                "text" => '',
                "span" => '<span class="fas fa-boxes text-success fa-md" data-toggle="tooltip" title="Товары прибыли"></span>',
            ],
            8 => [
                "title" => 'Товар отправлен на сборку',
                "icon" => 'fas fa-box-open text-info fa-md',
                "text" => '',
                "span" => '<span class="fas fa-box-open text-info fa-md" data-toggle="tooltip" title="Товар отправлен на сборку"></span>',
            ],
            9 => [
                "title" => 'Товар собран',
                "icon" => 'fas fa-box-open text-success fa-md',
                "text" => '',
                "span" => '<span class="fas fa-box-open text-success fa-md" data-toggle="tooltip" title="Товар собран"></span>',
            ],
            10 => [
                "title" => 'Товар отсутствует',
                "icon" => 'fas fa-box-open text-danger fa-md',
                "text" => '',
                "span" => '<span class="fas fa-box-open text-danger fa-md" data-toggle="tooltip" title="Товар отсутствует"></span>',
            ],
            11 => [
                "title" => 'Товары прибыли частично',
                "icon" => 'fas fa-boxes text-warning fa-md',
                "text" => '',
                "span" => '<span class="fas fa-boxes text-warning fa-md" data-toggle="tooltip" title="Товары прибыли частично"></span>',
            ],
            12 => [
                "title" => 'Товар реализован',
                "icon" => 'fas fa-check-circle text-success fa-md',
                "text" => '',
                "span" => '<span class="fas fa-check-circle text-success fa-md" data-toggle="tooltip" title="Товар реализован"></span>',
            ],
            13 => [
                "title" => 'Товар реализован частично',
                "icon" => 'fas fa-check-circle text-warning fa-md',
                "text" => '',
                "span" => '<span class="fas fa-check-circle text-warning fa-md" data-toggle="tooltip" title="Товар реализован частично"></span>',
            ],
            14 => [
                "title" => 'Складская позиция',
                "icon" => 'fa fa-archive text-success fa-md',
                "text" => '',
                "span" => '<span class="fa fa-archive text-success fa-md" data-toggle="tooltip" title="Складская позиция"></span>',
            ],
            15 => [
                "title" => 'Товар перемещен',
                "icon" => 'fa fa-retweet text-success fa-md',
                "text" => '',
                "span" => '<span class="fa fa-retweet text-success fa-md" data-toggle="tooltip" title="Товар перемещен"></span>',
            ],
            16 => [
                "title" => 'Товар перемещен частично',
                "icon" => 'fa fa-retweet text-warning fa-md',
                "text" => '',
                "span" => '<span class="fa fa-retweet text-warning fa-md" data-toggle="tooltip" title="Товар перемещен"></span>',
            ],
            17 => [
                "title" => 'Товар собран частично',
                "icon" => 'fas fa-box-open text-warning fa-md',
                "text" => '',
                "span" => '<span class="fas fa-box-open text-warning fa-md" data-toggle="tooltip" title="Товар собран частично"></span>',
            ],
            18 => [
                "title" => 'Отмена прибытия товара',
                "icon" => 'fas fa-ban text-danger fa-md',
                "text" => '',
                "span" => '<span class="fas fa-ban text-danger fa-md" data-toggle="tooltip" title="Отмена прибытия товара"></span>',
            ],
            19 => [
                "title" => 'Отмена проведения реализации',
                "icon" => 'fas fa-ban text-danger fa-md',
                "text" => '',
                "span" => '<span class="fas fa-ban text-danger fa-md" data-toggle="tooltip" title="Отмена проведения реализации"></span>',
            ],
            20 => [
                "title" => 'Товар оплачен',
                "icon" => 'fa fa-truck text-success fa-md',
                "text" => '',
                "span" => '<span class="fa fa-truck text-success fa-md" data-toggle="tooltip" title="Товар оплачен"></span>',
            ],
            21 => [
                "title" => 'Товары доставлены',
                "icon" => 'fas fa-truck-loading text-success fa-md',
                "text" => '',
                "span" => '<span class="fas fa-truck-loading text-success fa-md" data-toggle="tooltip" title="Товары доставлены"></span>',
            ],
            22 => [
                "title" => 'Товары в машине',
                "icon" => 'fas fa-truck-moving text-success fa-md',
                "text" => '',
                "span" => '<span class="fas fa-truck-moving text-success fa-md" data-toggle="tooltip" title="Товары в машине"></span>',
            ],
            23 => [
                "title" => 'Товары частично в машине',
                "icon" => 'fas fa-truck-moving text-warning fa-md',
                "text" => '',
                "span" => '<span class="fas fa-truck-moving text-warning fa-md" data-toggle="tooltip" title="Товары частично в машине"></span>',
            ],
            24 => [
                "title" => 'Разрыв связи',
                "icon" => 'fas fa-unlink text-danger fa-md',
                "text" => '',
                "span" => '<span class="fas fa-unlink text-danger fa-md" data-toggle="tooltip" title="Разрыв связи"></span>',
            ],
        ];
    }

    public function getStatusViewAttribute()
    {
        return (object)static::getStatus()[$this->status] ?? $this->status;
    }

    private static function getSupplyOrderDetailData($id)
    {
        return SupplyOrderDetail::where("sale_detail_id", $id)->first();
    }

    private static function getSOSDetailData($id)
    {
        return SupplyDetail::whereHas('supply', function ($query) {
            $query->where("status", 2)->where("type", 2);
        })->where("sdid", $id)->first();
    }

    static function getPurchase($id, $request = []): object
    {
        $data = new class
        {
            public $price = null;
            public $date = null;
            public $color = null;
            public $files = null;
            //data
            public $sales_detail = null;
            public $price_data = null;
            public $supply_order_detail = null;
            public $sos = null;
        };

        $data->sales_detail = $request["sales_detail"] ?? static::find($id);
        if (empty($data->sales_detail)) {
            $data->price = 0;
            return $data;
        }
        $cid = $request["cid"] ?? $data->sales_detail->cid;

        if (!is_null($id) && $cid == ($data->sales_detail->cid ?? null)) {
            //supply_order
            $supply_order_detail = static::getSupplyOrderDetailData($id);
            if (!empty($supply_order_detail)) {
                // $data->price = round($supply_order_detail->total / $supply_order_detail->count, 2);
                $data->price = $supply_order_detail->price_now;
                $data->date = empty($supply_order_detail->supply_order->date_bill) ? null : date("d.m.Y", strtotime($supply_order_detail->supply_order->date_bill));
                $data->color = "green";
                $data->supply_order_detail = $supply_order_detail;
                if (!empty($supply_order_detail->supply_order->id)) {
                    $data->files = \Supply\Support\SupplyOrderSupport::getAttach($supply_order_detail->supply_order->id);
                }
                return $data;
            }

            //sos
            $sos = static::getSOSDetailData($id);

            if (!empty($sos)) {
                $data->price = $sos->sos_price;
                $data->date = is_null($sos->sos_price_date) ? null : date("d.m.Y", strtotime($sos->sos_price_date));
                $data->sos = $sos;
                return $data;
            }
        }

        if ($cid == ($data->sales_detail->cid ?? null)) {
            //sales local
            if (!empty($data->sales_detail->s_price_local) || $data->sales_detail->s_price_local === "0" || $data->sales_detail->s_price_local === 0) {
                $data->price = $data->sales_detail->s_price_local;
                $data->color = "#b5b502";
                return $data;
            }
        }

        //purchase
        $data->price_data = \Price::getPrice($cid);
        $data->price = $data->price_data->price_purchase_;
        $data->date = isset($data->price_data->price_purchase_detail->price->date) ? date("d.m.Y", strtotime($data->price_data->price_purchase_detail->price->date)) : null;
        return $data;
    }

    static function getDatePost($id, $request = []): object
    {
        $data = (object)cc();
        $data->date_post = null;
        $data->color = null;

        $sales_detail = $request["sales_detail"] ?? static::find($id);

        //supply_order
        $supply_order_detail = $request["supply_order_detail"] ?? static::getSupplyOrderDetailData($id);
        if (!empty($supply_order_detail)) {
            $date_post = false;
            if (!empty($supply_order_detail->supply_order->date_ship)) {
                $date_post = date("d.m.Y", strtotime($supply_order_detail->supply_order->date_ship));
            }
            if (!empty($supply_order_detail->date_ship)) {
                $date_post = date("d.m.Y", strtotime($supply_order_detail->date_ship));
            }
            if (!empty($supply_order_detail->supply_order->date_end)) {
                $date_post = date("d.m.Y", strtotime($supply_order_detail->supply_order->date_end));
            }
            if (!empty($supply_order_detail->date_end)) {
                $date_post = date("d.m.Y", strtotime($supply_order_detail->date_end));
            }
            if ($date_post) {
                $data->date_post = $date_post;
                return $data;
            }
        }

        //sos
        $sos = $request["sos"] ?? static::getSOSDetailData($id);

        if (!empty($sos) && !empty($sos->date_post)) {
            $data->date_post = date("d.m.Y", strtotime($sos->date_post));
            return $data;
        }

        //sales local
        if (!empty($sales_detail->date_post)) {
            $data->date_post = date("d.m.Y", strtotime($sales_detail->date_post));
            $data->color = "red";
            return $data;
        }

        return $data;
    }

    static function getPurchaseFile($id, $request = [])
    {
        $data = (object)cc();
        $data->file_url = null;

        $sales_detail = $request["sales_detail"] ?? static::find($id);
        $cid = $request["cid"] ?? $sales_detail->cid;

        if ($cid == $sales_detail->cid) {
            //supply_order
            $supply_order_detail = $request["supply_order_detail"] ?? static::getSupplyOrderDetailData($id);
            if (!empty($supply_order_detail)) {
                $attach = \Boot\Models\Attach::where("module", "supply")->where("pid", $supply_order_detail->supply_o)->first();
                if (!empty($attach)) {
                    $data->file_url = "/download/{$attach->id}";
                    return $data;
                }
            }

            //sos
            $sos = $request["sos"] ?? static::getSOSDetailData($id);
            if (!empty($sos) && !empty($sos->sos_bill_aid)) {
                $data->file_url = "/download/{$sos->sos_bill_aid}";
                return $data;
            }
        }

        //purchase
        $price_data = $request["price_data"] ?? \Price::getPrice($cid);
        $data->file_url = $price_data->price_purchase_detail->price->attach->id ?? null;
        if (!empty($data->file_url)) {
            $data->file_url = "/download/{$data->file_url}";
        }
        return $data;
    }

    static function check_can_delete($id)
    {
        //check realization
        $realization_detail = \Realization\Model\RealizationDetail::where("sdid", $id)->first();
        if (!empty($realization_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите позицию из реализации!",
                "link" => "/administration/module/realization/order/{$realization_detail->rid}"
            ], JSON_UNESCAPED_UNICODE));
        }

        //check supply
        $supply_detail = SupplyDetail::whereHas("supply", function ($query) {
            $query->where("type", 1);
        })->where("sdid", $id)->first();
        if (!empty($supply_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите из заявки на закупку!",
                "link" => "/administration/module/supply/detail/{$supply_detail->sid}",
            ], JSON_UNESCAPED_UNICODE));
        }

        //check sos
        $supply_detail = SupplyDetail::whereHas("supply", function ($query) {
            $query->where("type", 2);
        })->where("sdid", $id)->first();
        if (!empty($supply_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите из заявки на сроки!",
                "link" => "/administration/module/supply/twodetail/{$supply_detail->sid}",
            ], JSON_UNESCAPED_UNICODE));
        }

        //check transfer
        $transfer_detail = StockTransferDetail::where("sdid", $id)->first();
        if (!empty($transfer_detail)) {
            throw new \Exception(json_encode([
                "error" => "Позиция имеет связь. Для продолжения удалите позицию из перемещения!",
                "link" => "/administration/module/storage/transfer/detail/{$transfer_detail->transfer_id}"
            ], JSON_UNESCAPED_UNICODE));
        }

        //check clone
        $sales_detail = \Sales\Models\SalesDetail::find($id);
        $clone_sales = new \Sales\CloneSales(["parent_id" => $sales_detail->sales_id, "children_id" => $sales_detail->id]);
        $clone_sales->check_can_delete();
    }
}
