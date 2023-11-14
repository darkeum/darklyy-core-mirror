<?php

namespace Logistics\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class LogisticsDeliveryDetail extends \Model\Model{
    use SoftDeletes;

    protected $fillable = ["logistics_delivery_id", "pid"];
}