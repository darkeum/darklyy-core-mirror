<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Promotions\Models;

use Boot\Support\Str;
use Sales\Model\Sales;
use Boot\Support\Facades\Auth;
use Boot\System\Core\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Boot\System\Core\Traits\HasResourceKeyTrait;

class Promotions extends \Boot\Models\Model{

    use SoftDeletes, HasResourceKeyTrait, HashIdTrait;

    protected $resourceKey = 'Promotions';

    protected $fillable = ["uid", "title", "slug", "short", "full", "type", "sales_id", "fulltitle", "description", "keywords", "preview", "preview_doc", "date_start", "date_end", "views", "tags", "status"];

    public function sales(){
        return $this->belongsTo(Sales::class);
    }

    protected $casts = [
        'preview' => "array",
        'tags' => "array",
        'date_start' => "date",
        'date_end' => "date",
    ];

    public static function boot(){
        parent::boot();
        
        static::creating(function($model){
            if(empty($model->slug)){
                $model->slug = Str::slug($model->title);               
            }
            if(empty($model->date_start)){
                $model->date_start = null;               
            } else {
                $model->date_start = date('Y-m-d', strtotime($model->date_start));      
            }
            if(empty($model->date_end)){
                $model->date_end = null;               
            } else {
                $model->date_end = date('Y-m-d', strtotime($model->date_end));      
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
                $preview = self::fileInit('promotions', $model->id, $model->preview['source']);      
                $preview_doc = self::fileInit('promotions', $model->id, $model->preview_doc);      
                $request['preview'] = $preview;
                $request['preview_doc'] = $preview_doc;
            }
            $slug =  $model->id.'-'.$model->slug;
            $request['slug'] = $slug;
            // dd($request)s;
            $model->update($request);
        });

        static::updating(function($model){
         
            if(empty($model->slug)){
                $model->slug = $model->id.'-'.Str::slug($model->title);               
            }
            if(!empty($model->preview)) {
                $model->preview = self::prepare_image($model->preview, 'files/promotions/'.$model->id.'/thumb');
            } else {
                $model->preview = [];
            }

            if(!empty($model->preview_doc)) {
                $preview_doc = str_replace("https://atri-energo.ru/", "", $model->preview_doc);
                $preview_doc = str_replace("atri-energo.ru/", "", $preview_doc);
                $preview_doc = rawurldecode($preview_doc); 
                $model->preview_doc =  $preview_doc;      
            }

            if(!empty($model->tags) && !is_array($model->tags)){
                $tags =  explode(',', $model->tags); 
                foreach ($tags as $key => $tag) {
                    $tags[$key] =  Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }  
            if(empty($model->date_start)){
                $model->date_start = null;               
            } else {
                $model->date_start = date('Y-m-d', strtotime($model->date_start));      
            }
            if(empty($model->date_end)){
                $model->date_end = null;               
            } else {
                $model->date_end = date('Y-m-d', strtotime($model->date_end));      
            }
        });

    }
   
    public static function fileInit($module, $id, $content, $what = 'temp')
    {
	    $temp = 'files/' . $module . '/' . $what;
	    $new = 'files/' . $module . '/' . $id;
        if(is_dir($temp)) {
            rename(ROOT.$temp, ROOT.$new);        
        }      
        return str_replace($temp, $new, $content);
    }

    public static function prepare_image($source, $purl) {  
        $dir = ROOT.$purl;
		if (!file_exists($dir)) {
			mkdir($dir, 0777,$recursive = true);
        }   
        $image = str_replace("https://atri-energo.ru/", "", $source);
        $image = str_replace("atri-energo.ru/", "", $image);
        $image = rawurldecode($image);        
        $file_full = $image;
        $src = ROOT . $file_full;
        $info = pathinfo($src);
        $ext = $info['extension'];
        $name = $info['filename'];
        switch (mb_strtolower($ext)) {
            default:
                $img = imageCreateFromJpeg($src);
                break;

            case 'png':
                $img = imageCreateFromPng($src);
                break;

            case 'jpg':
                $img = imageCreateFromJpeg($src);
                break;

            case 'gif':
                $img = imageCreateFromGif($src);
                break;
        }
        imageWebp($img, ROOT . $purl . '/'.$name. '.webp', 80);
        imagedestroy($img);
        $preview_array = array();
        $preview_array['source'] = $source;
        $preview_array['full'] = '/' .$image;
        $preview_array['full_webp'] = '/' . $purl . '/'. $name. '.webp';        
        return $preview_array;
    }   

    public function getUrlAttribute()
    {        
        $url = '/promotions/view/'.$this->slug;        
        return $url;
    }
    
}