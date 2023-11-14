<?php

namespace Economic;

use \Boot\Support\Facades\Auth;

class Balance{

    public $org;
    public $oid;
    public $date;
    public $action;
    public $value;
    public $pid;
    public $module;
    public $uid;
    public $reestr_id;

    public function middleware(){
        if(!isset($this->org))       throw new \Exception("Field `org` undefined");
        if(!isset($this->oid))       throw new \Exception("Field `oid` undefined");
        if(!isset($this->date))      throw new \Exception("Field `date` undefined");
        if(!isset($this->action))    throw new \Exception("Field `action` undefined");
        if(!isset($this->value))     throw new \Exception("Field `value` undefined");
        if(!isset($this->pid))       throw new \Exception("Field `pid` undefined");
        if(!isset($this->module))    throw new \Exception("Field `module` undefined");
        //if reestr_id is null, then broken
        // if(!isset($this->reestr_id))    throw new \Exception("Field `reestr_id` undefined");
    }

    public function create(){
        $this->middleware();
        $this->uid = $this->uid ?? Auth::id();
        $reestr_id = empty($this->reestr_id) ? null : $this->reestr_id;
        $balance = \Economic\Model\Balance::get_balance($this->org, $this->oid, $this->date, $reestr_id);
        $bb = $balance == null ? 0 : $balance->balance;
        if($this->action == "plus"){
            $b = $bb + $this->value;
        } else if($this->action == "minus"){
            $b = $bb - $this->value;
        } else {
            throw new \Exception("Action undefined!");
        }

        \Economic\Model\Balance::create(["org" => $this->org, "oid" => $this->oid, "date" => $this->date, "balance" => $b, "pid" => $this->pid, "module" => $this->module, "uid" => $this->uid, "action" => $this->action, "reestr_id" => $reestr_id]);
    }
}