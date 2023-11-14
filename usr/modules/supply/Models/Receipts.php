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

use Boot\Models\Model;
use Boot\Support\Carbon;
use Boot\Support\Module;
use Boot\Support\Facades\Auth;
use Supply\Models\SupplyOrder;
use Supply\Models\ReceiptsDetail;
use Organizations\Model\Organizations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipts extends Model
{
    use SoftDeletes;

    protected $fillable = ["numb_counter", "numb", "sales_id", "supply_id", "org", "object", "stock_id", "uid", "oid", "oid_name", "inn", "date", "date_receipts", "doc_number", "type_doc", "waybill_number", "total", "nds", "nds_includes", "active", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
            $model->date = empty($model->date) ? Carbon::now() : $model->date;
            $numb_array = Module::numb('receipts', $model->org, $model->date);
            $model->numb = $numb_array['numb'];
            $model->numb_counter = $numb_array['numb_counter'];
        });

        static::created(function ($model) {
            relationsSave('supply_order', $model->supply_id, 'receipts', $model->id);
        });
    }

    public function receipts_detail()
    {
        return $this->hasMany(ReceiptsDetail::class, "rid");
    }

    public function supply_order()
    {
        return $this->belongsTo(SupplyOrder::class, "supply_id");
    }

    public function organization()
    {
        return $this->belongsTo(Organizations::class, "oid");
    }
}
