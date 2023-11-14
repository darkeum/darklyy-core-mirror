<?php

namespace Catalog_v2\Model;

use \Boot\Support\Facades\Auth;

class CatalogGroups extends \Model\Model{

    protected $fillable = ["name", "uid", "category", "comment"];

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });
    }
}