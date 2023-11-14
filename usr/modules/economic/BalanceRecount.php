<?php

namespace Economic;

class BalanceRecount{
    use ActController;

    public $action = "";
    public $org;
    public $oid;

    public function init_from_balance($balance){
        $this->org = $balance->org;
        $this->oid = $balance->oid;
        $this->date_start = $balance->date;
    }

    public function recount(){
        $this->get_date_end();
        $this->delete_records();
        if($this->action == "set"){
            $this->date_start = date("Y-m-d", strtotime("{$this->date_start} +1 day"));
        }
        $this->init_data();
        $records = $this->get_records();
        $this->get_initial_balance();
        foreach($records as $record){
            $action = false;
            if(!empty($record->debit)){
                $this->initial_balance_contract[ $record->contract_id ]->balance += $record->debit;
                $action = "plus";
            }
            if(!empty($record->credit)){
                $this->initial_balance_contract[ $record->contract_id ]->balance -= $record->credit;
                $action = "minus";
            }
            if($action){
                \Economic\Model\Balance::create(["org" => $this->org, "oid" => $this->oid, "date" => $record->date, "balance" => $this->initial_balance_contract[ $record->contract_id ]->balance, "pid" => $record->id, "module" => $record->module, "uid" => $record->uid, "action" => $action, "reestr_id" => $record->contract_id]);
            }
        }
    }

    public function get_date_end(){
        #получить дату до какой даты генерировать записи
        $this->date_end = date("Y-m-d");
        $b = \Economic\Model\Balance::where("org", $this->org)
        ->where("oid", $this->oid)
        ->orderByDesc("date")
        ->first();
        if(!is_null($b) && ( strtotime($this->date_end) < strtotime($this->date_start) )) $this->date_end = date("Y-m-d", strtotime($this->date_start));
    }

    public function delete_records(){
         #удалить предыдущие записи
        \Economic\Model\Balance::whereDate('date', '>=', date("Y-m-d", strtotime($this->date_start)))
        ->where("org", $this->org)
        ->where("oid", $this->oid)
        ->where("action", "!=", "set")
        ->delete();
    }
}