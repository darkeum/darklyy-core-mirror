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

use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogAttribute extends \Boot\System\Database\Model\Model{
    use \Boot\Traits\CacheAll;
    use SoftDeletes;
    
    protected $fillable = ["id", "name", "type", "blocked", "extended"];

    protected $appends = ['type_view'];

    public function attribute_category(){
        return $this->hasMany(\Catalog\Model\CatalogAttributeCategory::class, "attribute_id");
    }

    public function attribute_category_one(){
        return $this->hasOne(\Catalog\Model\CatalogAttributeCategory::class, "attribute_id");
    }

    public function attribute_value(){
        return $this->hasMany(\Catalog\Model\CatalogAttributeValue::class, "attribute_id");
    }

    public function attribute_relation(){
        return $this->hasMany(\Catalog\Model\CatalogAttributeRelation::class, "attribute_id");
    }

    static function getType(){
        $types = \Catalog\Attribute\Attribute::getTypes();
        $data = [];
        foreach($types as $type => $type_class){
            $data[ $type ] = $type_class::getRus();
        }
        return $data;
    }

    public function getTypeViewAttribute(){
        return static::getType()[ $this->type ] ?? $this->type;
    } 
}