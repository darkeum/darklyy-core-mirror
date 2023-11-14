<?php

namespace Economic\Model;

class Balance extends \Model\Model{

    protected $fillable = ["org", "oid", "reestr_id", "balance", "date", "pid", "module", "uid", "action"];

    protected $casts = [
        'date' => 'date',
    ];

    static public function get_balance($org, $oid, $date, $reestr_id = null){
        return \Economic\Model\Balance::where("org", $org)
        ->where("oid", $oid)
        ->whereDate("date", "<=", $date)
        ->where("reestr_id", $reestr_id)
        ->orderByDesc("date")
        ->orderByDesc("id")
        ->first();
    }

    public function org_(){
        return $this->belongsTo(\Organizations\Model\Org::class, "org");
    }

    public function reestr(){
        return $this->belongsTo(\Reestr\Models\Reestr::class);
    }
}