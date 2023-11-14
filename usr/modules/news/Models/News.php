<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace News\Models;

use Boot\Models\Model;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use \Boot\System\Categories\Categories;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HashIdTrait;
    use HasResourceKeyTrait;
    
    use SoftDeletes;

    /**
     * Ключ ресурса, который будет использоваться в сериализованных ответах.
     *
     * @var string
     */
    protected $resourceKey = 'News';
    /**/

    protected $fillable = [
        "uid",
        "category",

        // "published_at",

        "tags",
        "slug",
        "preview",
        "title",
        "short",

        "full",
        "fulltitle",

        "keywords",
        "description",

        "views",
        "favorite",
        "status"
    ];

    protected $casts = [
        'published_at'  => "datetime",
        'tags'          => "array",
        'preview'       => "array",
    ];

    protected $dates = [
        'deleted_at',
        'published_at',
        'created_at',
        'updated_at',
    ];

    public static function boot(){

        parent::boot();
        
        static::creating(function($model){

            if(empty($model->slug)){
                $model->slug = Str::slug($model->title);               
            }

            if(empty($model->published_at)){
                $model->published_at = null;               
            } else {
                $model->published_at = date('Y-m-d H:i:s', strtotime($model->published_at));      
            }

            if(!empty($model->preview)) {
                $model->preview = ['source' => $model->preview];
            } else {
                $model->preview = [];
            }

            $model->uid = Auth::id(); 
            if(!empty($model->tags)){
                $tags =  explode(',', $model->tags); 
                foreach ($tags as $key => $tag) {
                    $tags[$key] =  Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }       
        });

        static::created(function($model){
            $request = [];

            if(!empty($model->preview)){
                $preview = self::fileInit('news', $model->id, $model->preview['source']);
                $request['preview'] = $preview;
            }

            $slug =  $model->id.'-'.$model->slug;
            $request['slug'] = $slug;
            $model->update($request);
        });

        static::updating(function($model){
         
            if(empty($model->slug)){
                $model->slug = $model->id.'-'.Str::slug($model->title['title']);               
            }
            if(!empty($model->tags) && !is_array($model->tags)){
                $tags =  explode(',', $model->tags); 
                foreach ($tags as $key => $tag) {
                    $tags[$key] =  Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }

            if(empty($model->published_at)){
                $model->published_at = null;               
            } else {
                $model->published_at = date('Y-m-d H:i:s', strtotime($model->published_at));
            }            
        });

    }

    public static function fileInit($module, $id, $content, $what = 'temp')
    {
	    $temp = 'files/' . $module . '/' . $what;
	    $new = 'files/' . $module . '/' . $id;
        rename(ROOT.$temp, ROOT.$new);

        return str_replace($temp, $new, $content);
    }

    //public function cats()
    //{
    //    return $this->hasMany(\Boot\Models\Categories::class, "id", 'category');
    //}

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    }

    public function lang()
    {
        return $this->morphMany(\Boot\Models\Langs::class, '', 'module', 'postId');
    }

    /*
    public function getTextClearAttribute()
    {
         return ee(strip_tags($this->lang->short));
    }
    */

    public function getUrlAttribute()
    {
        if(!empty($this->category)){
            $cat = new Categories;
            $url = '/news/'.$cat->getCategoryFullUrl($this->category).'view/'.$this->slug;
        } else {
            $url = '/news/view/'.$this->slug;
        }
        return $url;
    }

}