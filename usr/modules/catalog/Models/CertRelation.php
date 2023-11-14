<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\Models;

use Boot\Models\Model;

class CertRelation extends Model
{
    protected $fillable = ["cid", "cert_id", "hash", "date", "status"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->hash = empty($model->hash) ? "{$model->cid}_{$model->cert_id}" : $model->hash;
            $model->date = empty($model->date) ? date("Y-m-d") : $model->date;
            $model->status = empty($model->status) ? 1 : $model->status;
        });
    }

    public function cert()
    {
        return $this->belongsTo(\Catalog\Models\Cert::class, "cert_id", "id");
    }
}
