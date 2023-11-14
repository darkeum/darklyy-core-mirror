<?php

namespace Catalog\Model;

use Boot\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogAttributeValue extends \Boot\System\Database\Model\Model{
    use SoftDeletes;

    protected $fillable = ["attribute_id", "value", "value_save", "position"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->value = Str::of($model->value)->trim();
            $model->value_save = empty($model->value_save) ? $model->value : $model->value_save;
        });
    }

    public function catalog_attribute_relation(){
        return $this->hasMany(\Catalog\Model\CatalogAttributeRelation::class, "attribute_value_id");
    }
}