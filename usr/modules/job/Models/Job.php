<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Job\Models;

use Boot\Models\Model;
use Boot\System\Categories\Categories;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        "uid",
        "category",
        "title",
        "slug",
        "description",
        "duties",
        "conditions",
        "requirements",
        "responsible",
        "salary",
        "create_at",
        "update_at",
        "status"
    ];

    protected $casts = [
        'duties' => "array",
        'conditions' => "array",
        'requirements' => "array",
    ];

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    }

    public function getUrlAttribute()
    {
        if (!empty($this->category)) {
            $cat = new Categories;
            $url = '/job/' . $cat->getCategoryFullUrl($this->category) . 'view/' . $this->slug;
        } else {
            $url = '/job/view/' . $this->slug;
        }
        return $url;
    }
}
