<?php

namespace Calculation\Model;

class HeatSolution extends \Model\Model{
    protected $fillable = ["heat_id", "solution_id", "solution", "price", "deliveryWeeks", "effectiveSurface", "weight", "height", "width", "length", "detailed", "response", "result", "aid", "cid", "status"]; 

    protected $casts = [
        'detailed' => "array",
        'response' => "array",
        'aid' => "array",
        'cid' => "array",
        'result' => "array",
    ];

    public function heat()
    {
        return $this->belongsTo(\Calculation\Model\Heat::class, "heat_id");
    }

}