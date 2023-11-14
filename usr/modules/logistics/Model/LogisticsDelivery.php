<?php

namespace Logistics\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use \Boot\Support\Facades\Auth;

//RULES
//status: 1 - заявка поступила; 2 - заявка отправлена водителю; 3 - заявка доставлена
class LogisticsDelivery extends \Model\Model{
    use SoftDeletes;

    protected $fillable = ["module", "pid", "uid", "driver", "address", "coordinates", "contact_person", "phone", "delivery_date", "delivery_from", "delivery_to", "comment", "delivery_type", "delivery_price", "status"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }

    static function getDeliveryType(){
        return [1 => "фиксированная", "включёно в счёт", "за счёт компании"]; 
    }

    public function getDeliveryTypeViewAttribute(){
        return static::getDeliveryType()[ $this->delivery_type ] ?? $this->delivery_type;
    }

    public function getModule(){
        switch($this->module){
            case "realization":
                return \Realization\Model\Realization::findOrFail($this->pid);
            break;
            case "stock_transfer":
                return \Storage\Model\StockTransfer::findOrFail($this->pid);
            break;
        }
    }
}