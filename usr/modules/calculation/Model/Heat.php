<?php

namespace Calculation\Model;

class Heat extends \Model\Model{
  protected $fillable = ["uid", "opp_id", "heatData", "request", "status"];

  protected $casts = [
      'heatData' => "array",
      'request' => "array",
  ];   
}