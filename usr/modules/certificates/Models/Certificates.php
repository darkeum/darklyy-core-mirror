<?php

namespace Certificates\Models;

use \Boot\Models\Model;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;
use \Boot\System\Categories\Categories;


class Certificates extends Model
{

    use SoftDeletes;

    protected $fillable = [
        "category",
        "slug",
        "title",
        "preview",
        "pdf",
        "org_name",
        "short",
        "full",
        "numb",
        "date_start",
        "date_end",
        "uid",
        "fulltitle",
        "keywords",
        "description",
        "views",
        "status"
    ];
    
    protected $casts = [
        'tags' => "array",
        'preview' => "array",
        'date_start' => "date",
        'date_end' => "date",
    ];

    // protected $dates = [
    //     'date_end',
    //     'date_start',
    // ];

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
                $preview = self::fileInit('certificates', $model->id, $model->preview['source']);      
                $request['preview'] = $preview;
            }
            $slug =  $model->id.'-'.$model->slug;
            $request['slug'] = $slug;          
            $model->update($request);
        });

        static::updating(function($model){
         
            if(empty($model->slug)){
                $model->slug = $model->id.'-'.Str::slug($model->title);               
            }
            //if(!empty($model->preview)) {
            //    $model->preview = self::prepare_image($model->preview, 'files/certificates/'.$model->id.'/thumb');
            //    $model->pdf = self::prepare_pdf($model->preview['full'], $model->id);
            //} else {
            //    $model->preview = [];
            //}
            if(!empty($model->tags) && !is_array($model->tags)){
                $tags =  explode(',', $model->tags); 
                foreach ($tags as $key => $tag) {
                    $tags[$key] =  Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }                      
        });

    }

    public function setDateEndAttribute($date)
    {
        $this->attributes['date_end'] = empty($date) ? null : Carbon::parse($date);
    }

    public static function prepare_pdf($source, $rid) { 
        $purl = 'files/certificates/'.$rid.'/';
        $file_full_pdf = $purl.'pdf_' .$rid.'.pdf';
		$src_pdf = ROOT.$file_full_pdf;		
        if (file_exists($src_pdf)) {
			@unlink($src_pdf);
		}
        $im=new \Imagick(ROOT.$source);
		$im->setImageFormat('pdf');
		$im->writeImage($src_pdf);
        return '/'.$file_full_pdf;
    }

    public static function fileInit($module, $id, $content, $what = 'temp')
    {
	    $temp = 'files/' . $module . '/' . $what;
	    $new = 'files/' . $module . '/' . $id;
        rename(ROOT.$temp, ROOT.$new);        
        return str_replace($temp, $new, $content);
    }

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    } 

    public function getUrlAttribute()
    {
        if(!empty($this->category)){
            $cat = new Categories;
            $url = '/certificates/'.$cat->getCategoryFullUrl($this->category).'view/'.$this->slug;
        } else {
            $url = '/certificates/view/'.$this->slug;
        }
        return $url;
    }


}