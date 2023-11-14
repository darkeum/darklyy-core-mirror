<?php

namespace Partner\Models;

use \Boot\Models\Model;
use \Boot\System\Categories\Categories;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;

class Partner extends Model {
    protected $fillable = [
        "uid",
        "category",
        "site",
        "site_url",
        "email",
        "phone",
        "location",
        "address",
        "page",
        "slug",
        "preview",
        "preview_mini",
        "main_image",
        "keywords",
        "description",
        "views",
        "color_class",
        "published_at",
        "status"
    ];

    protected $casts = [
        // 'tags' => "array",
        'preview' => "array",
        // 'date_start' => "date",
        // 'date_end' => "date",
    ];

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    } 

    public function getUrlAttribute()
    {
        if(!empty($this->category)){
            $cat = new Categories;
            $url = '/partner/'.$cat->getCategoryFullUrl($this->category).'view/'.$this->slug;
        } else {
            $url = '/partner/view/'.$this->slug;
        }
        return $url;
    }
    //

}