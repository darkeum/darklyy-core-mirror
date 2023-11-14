<?php

namespace Portfolio\Models;

use Boot\Models\Model;
use \Boot\System\Categories\Categories;
use Illuminate\Database\Eloquent\SoftDeletes;
class Portfolio extends Model {
    use SoftDeletes;
    protected $fillable = [
        "uid",
        "category",
        "title",
        "subtitle",
        "slug",
        "description",
        "full",
        "preview",
        "gallery",
        "address",
        "coord",
        "media",
        "tags",
        "published_at",
        "status"
    ];

    protected $casts = [
        'preview' => "array",
        'gallery' => "array",
        'media' => "array",
        'tags' => "array",
        'published_at' => "datetime",
    ];

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    }

    public function getUrlAttribute()
    {
        if(!empty($this->category)){
            $cat = new Categories;
            $url = '/portfolio/'.$cat->getCategoryFullUrl($this->category).'view/'.$this->slug;
        } else {
            $url = '/portfolio/view/'.$this->slug;
        }
        return $url;
    }

}