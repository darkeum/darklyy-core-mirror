<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\Models;

use Orgs\Models\Org;
use Boot\Models\Model;

class BankUpload extends Model
{
    protected $fillable = ["uid", "org", "date", "date_add", "balance_in", "balance_out", "file", "status"];

    public function bank_detail()
    {
        return $this->hasMany(BankDetail::class, "upload_id");
    }

    public function org_()
    {
        return $this->belongsTo(Org::class, "org");
    }
}
