<?php

namespace Boiler\Model;

class BoilerBidType extends \Boot\Models\Model{
    protected $fillable = [
        "bid_id",
        "section",
        "type",
        "allow_category",
        "comment",
        "client_comment",
        "folder_id",
        "uid",
        "created_at",
        "updated_at",
        "position",
        "active_color",
        "color"
    ];
    public function boiler_bid()
    {
        return $this->belongsTo(BoilerBid::class, "bid_id");
    }

}