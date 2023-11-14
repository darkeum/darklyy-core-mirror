<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Supply\Models;

use Orgs\Models\Org;
use Boot\Models\Model;
use Boot\Support\Carbon;
use Boot\Support\Module;
use Boot\Support\Facades\Auth;
use Supply\Models\SupplyOrderDetail;
use Organizations\Model\Organizations;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyOrder extends Model
{
    use SoftDeletes;

    protected $fillable = ["numb_counter", "numb", "org", "oid", "uid", "object", "sales_id", "total", "bill_code", "date_pay", "delivery", "date_bill", "date_work", "date_ship", "date_end", "date_create", "comment", "comment_control", "nds", "nds_includes", "control", "control_send", "pay", "arrived", "admission", "close", "status"];

    protected $casts = [
        'comment_control' => "array",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date_create = empty($model->date_create) ? Carbon::now() : $model->date_create;
            $numb_array = Module::numb('supply_order', $model->org, $model->date_create);
            $model->numb = $numb_array['numb'];
            $model->numb_counter = $numb_array['numb_counter'];
        });
    }

    public function supply_order_detail()
    {
        return $this->hasMany(SupplyOrderDetail::class, "supply_o");
    }

    public function org_()
    {
        return $this->belongsTo(Org::class, "org");
    }

    public function organization()
    {
        return $this->belongsTo(Organizations::class, "oid");
    }
}
