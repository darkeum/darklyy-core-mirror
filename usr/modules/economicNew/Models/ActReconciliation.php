<?php

namespace EconomicNew\Models;

use Boot\Support\Module;

class ActReconciliation extends \Model\Model{
    /*
        fill_type: 0 - По нашей организации и контрагенту; 1 - Только по нашей организации;
    */
    protected $fillable = [
        "org", "oid", "uid_created",
        "uid", "numb_counter", "numb",
        "comment", "fill_type", "agreed",
        "date", "date_start", "date_end",
        "org_data", "org_director", "oid_data",
        "records", "initial_balance", "initial_balance_contract",
        "final_balance", "break_contracts", "display_full_document_titles",
        "display_invoices", "active"];

    protected $appends = ["title"];

    protected $casts = [
        'break_contracts' => 'boolean',
        'display_full_document_titles' => 'boolean',
        'display_invoices' => 'boolean',
        'active' => 'boolean',
        'agreed' => 'boolean',
        'records' => 'object',
        'initial_balance_contract' => 'object',
        'org_data' => 'object',
        'org_director' => 'object',
        'oid_data' => 'object',
    ];

    protected $attributes = [
        'break_contracts' => 0,
        'display_full_document_titles' => 0,
        'display_invoices' => 0,
        'active' => 0,
        'fill_type' => 0,
        'agreed' => 0,
    ];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
            if(empty($model->numb)){
                $numb = Module::numb('act_reconciliation', $model->org, $model->date);
                $model->numb = $numb["numb"];
                $model->numb_counter = $numb["numb_counter"];
            }
            $model->comment = !empty($model->comment) ?: null;
        });
    }

    public function getTitleAttribute(){
        $title = $this->id ? "{$this->numb} от ".date("d.m.Y", strtotime($this->date)) : "(создание)";
        return "Акт сверки расчетов с контрагентом {$title}";
    }
}

