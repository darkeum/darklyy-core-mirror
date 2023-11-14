<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sales\Models;

use Boot\Models\Model;
use Boot\Models\Relations;
use Boot\Traits\SoftDeletesDel;
use Task\Model\Task;

class Sales extends Model
{
    use SoftDeletesDel;

    protected $fillable = ["del", "hidden_site", "numb", "numb_counter", "uid", "object", "address", "delivery_type", "oid", "date", "org", "nds_includes", "stock_id", "clone", "clone_active", "is_clone", "uid_send", "uid_maintenance", "uoif", "confirm_supply_order", "title", "date_add", "date_end", "comment", "comment_end", "nds", "total", "pay", "pay_sum", "sos", "supply", "realization", "reestr", "allow_shipment", "sales_type", "sales_cat", "status", "status_client", "end", "end_date", "md5", "source_appeal", "task_id", "task_title", "task_date_end", "task_availability", "market_segment", "by_field_activity", 'archive'];

    protected $casts = [
        'confirm_supply_order' => "array",
    ];

    public function sales_detail()
    {
        return $this->hasMany(\Sales\Model\SalesDetail::class);
    }

    public function client()
    {
        return $this->hasMany(\Sales\Model\SalesUsers::class);
    }

    public function organization()
    {
        return $this->belongsTo(\Organizations\Model\Organizations::class, "oid");
    }

    public function object_()
    {
        return $this->belongsTo(\Object\Model\ObjectModel::class, "object");
    }

    public function task()
    {
        return $this->belongsTo(\Task\Model\Task::class, "task_id");
    }

    //public function tasks()
    //{
    //    return $this->hasMany(Relations::class, 'pid_in')
    //        //->where('module_in', 'sales')
    //    ;
    //}

    public function user()
    {
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }

    public function user_maintenance()
    {
        return $this->belongsTo(\Boot\Models\Users::class, "uid_maintenance");
    }

    public function org_()
    {
        return $this->belongsTo(\Organizations\Model\Org::class, "org");
    }

    public function stock()
    {
        return $this->belongsTo(\Stocks\Models\Stock::class, "stock_id");
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->date = empty($model->date) ? \Carbon\Carbon::now() : date('Y-m-d H:i:s', strtotime($model->date));
            $numb_array = \Boot\Support\Module::numb('sales', $model->org, $model->date);
            $model->numb = $numb_array['numb'];
            $model->numb_counter = $numb_array['numb_counter'];

            $orgInfo = getOrgInfo($model->org);
            if (empty($model->stock_id)) {
                $stock_id_default = $orgInfo["org"]["stock_id_default"];
                $model->stock_id = $stock_id_default;
            }

            $model->date_add = empty($model->date_add) ? time() : $model->date_add;
            $model->nds_includes = empty($model->nds_includes) ? $orgInfo["org"]["nds_includes"] : $model->nds_includes;
        });

        static::created(function ($model) {

            if (!empty($model->oid)) static::contract_default($model);

            $obj_flag = false;
            $object_id = "";
            if (!empty($model->object)) {
                $object_id = $model->object;
                $obj_flag = true;
            }

            $obj_name = "";
            if ($obj_flag) {
                $obj = get_object_data($object_id);
                $obj_name = "(" . $obj["name"] . ")";
            }

            $chat = new \Chat();
            $chat->create_chat("sales", $model->id, null, "Продажа №" . $model->numb . " " . $obj_name, 1);

            $model->setLog($model, ["comment" => "Создание продажи №" . $model->numb, "pid" => $model->id]);

            if ($obj_flag) {
                relationsSave('object', $object_id, 'sales', $model->id);
            }
        });
    }

    static public function contract_default($model)
    {
        $reestr_id = \Organizations\Model\Organizations::find($model->oid)->reestr_id;
        if (!empty($reestr_id)) {
            $relation = new \Relations;
            $relation->add(["module_in" => "reestr", "pid_in" => $reestr_id, "module_out" => "sales", "pid_out" => $model->id]);
        }
    }

    public function re_status_client()
    {
        $this->status_client = 4;
        $sc6 = true;
        $sc7 = true;
        foreach ($this->sales_detail as $sd) {
            if ($sd->status_client < 6) {
                $sc6 = false;
            }
            if ($sd->status_client != 7) {
                $sc7 = false;
            }
            if ($sd->status_client > 4) {
                $this->status_client = 5;
            }
        }
        if ($sc6) $this->status_client = 6;
        if ($sc7) $this->status_client = 7;

        $this->save();
    }

    static function getSalesType()
    {
        return [
            0 => "Не указана",
            1 => "Заказ",
            2 => "Заявка на производство",
            3 => "Розничная торговля",
            4 => "Блочные решения",
            5 => "Мониторинг цен",
        ];
    }

    public function getSalesTypeViewAttribute()
    {
        return static::getSalesType()[$this->sales_type] ?? $this->sales_type;
    }

    static function getStatusClient($type = 'text')
    {
        $text = [
            1 => "Создан",
            2 => "На согласовании",
            3 => "Согласован",
            4 => "Размещён",
            5 => "Доставляется",
            6 => "Доставлен",
            7 => "Отгружен",
        ];
        switch ($type) {
            case 'icon':
                return [
                    1 => '<i class="fa-light fa-user-plus"    data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[1] . '"></i>',
                    2 => '<i class="fa-light fa-user-tie"     data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[2] . '"></i>',
                    3 => '<i class="fa-light fa-user-tie"     data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[3] . '"></i>',
                    4 => '<i class="fa-light fa-user-check"   data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[4] . '"></i>',
                    5 => '<i class="fa-light fa-truck"        data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[5] . '"></i>',
                    6 => '<i class="fa-light fa-truck"        data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[6] . '"></i>',
                    7 => '<i class="fa-light fa-check-circle" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="' . $text[7] . '"></i>',
                ];
                break;
            case 'color':
                return [
                    1 => "primary",
                    2 => "warning",
                    3 => "success",
                    4 => "success",
                    5 => "warning",
                    6 => "success",
                    7 => "success",
                ];
                break;

            case 'text':
            default:
                return $text;
                break;
        }
    }

    public function getStatusClientViewAttribute()
    {
        return static::getStatusClient()[$this->status_client] ?? $this->status_client;
    }

    public function getStatusClientIconAttribute()
    {
        return static::getStatusClient('icon')[$this->status_client] ?? $this->status_client;
    }

    public function getStatusClientColorAttribute()
    {
        return static::getStatusClient('color')[$this->status_client] ?? $this->status_client;
    }

    static function getSourceAppeal()
    {
        return ["Городской телефон", "Личный телефон", "Встреча", "Личная почта", "Общая почта", "От коллег", "Messenger", "Тендер", "Сайт", "Другое", "От коллег (Наш проект)", "От сторонних заказчиков (Наш проект)"];
    }

    public function getSourceAppealViewAttribute()
    {
        return static::getSourceAppeal()[$this->source_appeal] ?? $this->source_appeal;
    }

    static function getMarketSegment()
    {
        return ["ЖКХ и реконструкция", "Промышленность", "Новое строительство", "Муниципальный объект",];
    }

    public function getMarketSegmentViewAttribute()
    {
        return static::getMarketSegment()[$this->market_segment] ?? $this->market_segment;
    }

    static function getByFieldActivity()
    {
        return ["Комплектовщик", "Прямой закупщик", "Монтажник", "Проектировщик"];
    }

    public function getByFieldActivityViewAttribute()
    {
        return static::getByFieldActivity()[$this->by_field_activity] ?? $this->by_field_activity;
    }

    static function getSalesCat()
    {
        return ["Не указан", "Действующий контрагент", "Новый контрагент", "Сделка сопровождения"];
    }

    public function getSalesCatViewAttribute()
    {
        return static::getSalesCat()[$this->sales_cat] ?? $this->sales_cat;
    }

    static function getPay()
    {
        return ["Не оплачен", "Частично оплачен", "Оплачен"];
    }

    public function getPayViewAttribute()
    {
        return static::getPay()[$this->pay] ?? $this->pay;
    }
}
