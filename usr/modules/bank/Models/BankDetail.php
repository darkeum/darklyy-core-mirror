<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\Models;

use Orgs\Models\Org;
use Boot\Models\Model;
use Organizations\Model\Organizations;
use Organizations\Model\OrganizationsBank;

class BankDetail extends Model
{
    protected $fillable = ["hash", "numb", "org", "organizations_bank_id", "type", "pay_in", "date", "date_create", "sum", "payer", "payer_bank", "recipient", "recipient_bank", "comment", "upload_id", "module", "status"];

    public function bank_pays()
    {
        return $this->hasMany(BankPay::class);
    }

    public function org_()
    {
        return $this->belongsTo(Org::class, "org");
    }

    public function organizations_bank()
    {
        return $this->belongsTo(OrganizationsBank::class, "organizations_bank_id");
    }

    public function payer_()
    {
        return $this->belongsTo(Organizations::class, "payer");
    }

    public function payer_bank_()
    {
        return $this->belongsTo(OrganizationsBank::class, "payer_bank");
    }

    public function recipient_()
    {
        return $this->belongsTo(Organizations::class, "recipient");
    }

    public function recipient_bank_()
    {
        return $this->belongsTo(OrganizationsBank::class, "recipient_bank");
    }

    public function bank_upload()
    {
        return $this->belongsTo(BankUpload::class, "upload_id");
    }

    static function getPayIn()
    {
        return [
            0 => __('bank::main.operations.out'),
            1 => __('bank::main.operations.in'),
        ];
    }

    public function getPayInViewAttribute()
    {
        return static::getPayIn()[$this->pay_in] ?? $this->pay_in;
    }

    static function getType()
    {
        return [__('bank::main.types.bank.order'), __('bank::main.types.payment.request'), __('bank::main.types.payment.order'), __('bank::main.types.collection.order')];
    }

    public function getTypeViewAttribute()
    {
        return static::getType()[$this->type] ?? $this->type;
    }
}
