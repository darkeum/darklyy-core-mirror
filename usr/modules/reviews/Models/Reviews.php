<?php

namespace Reviews\Models;

use Boot\Models\Model;
use \Boot\System\Categories\Categories;
use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Reviews extends Model
{

    use SoftDeletes;

    /**
     * Ключ ресурса, который будет использоваться в сериализованных ответах.
     *
     * @var string
     */
    protected $resourceKey = 'Reviews';
    /**/

    protected $fillable = [
        "uid",
        "oid",
        "category",
        "type",
        "slug",
        "title",
        "subtitle",
        "content",
        "preview",
        "pdf",
        "city",
        "date_coop",
        "tags",
        "fulltitle",
        "description",
        "keywords",
        "views",
        "status"
    ];

    protected $casts = [
        'preview' => "array",
        'tags' => "array",
        'date_coop' => "date",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
            if (empty($model->date_coop)) {
                $model->date_coop = null;
            } else {
                $model->date_coop = date('Y-m-d', strtotime($model->date_coop));
            }
            if (!empty($model->preview)) {
                $model->preview = ['source' => $model->preview];
            } else {
                $model->preview = [];
            }
            $model->uid = Auth::id();
            if (!empty($model->tags)) {
                $tags = explode(',', $model->tags);
                foreach ($tags as $key => $tag) {
                    $tags[$key] = Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }
        });

        static::created(function ($model) {
            $request = [];
            // if (!empty($model->preview)) {
            //     $preview = self::fileInit('reviews', $model->id, $model->preview['source']);
            //     $request['preview'] = $preview;
            // }
            $slug = $model->id . '-' . $model->slug;
            $request['slug'] = $slug;
            // dd($request)s;
            $model->update($request);
        });

        static::updating(function ($model) {

            if (empty($model->slug)) {
                $model->slug = $model->id . '-' . Str::slug($model->title['title']);
            }

            //if (!empty($model->preview)) {

            // Проба новый вариант
            // prepare_image($model->preview['source'] ?? false,'files/news/'.$model->id.'/thumb' );

            //    $model->preview = self::prepare_image($model->preview, 'files/reviews/' . $model->id . '/thumb');
            //    $model->pdf = self::prepare_pdf($model->preview['full'], $model->id);
            //} else {
            //    $model->preview = [];
            //}
            if (!empty($model->preview)) {
                $model->preview = ['source' => $model->preview];
            } else {
                $model->preview = [];
            }
            if (!empty($model->tags) && !is_array($model->tags)) {
                $tags = explode(',', $model->tags);
                foreach ($tags as $key => $tag) {
                    $tags[$key] = Str::ucfirst(Str::trim($tag));
                }
                $model->tags = $tags;
            }
            if (empty($model->date_coop)) {
                $model->date_coop = null;
            } else {
                $model->date_coop = date('Y-m-d', strtotime($model->date_coop));
            }
            if (!empty($model->content)) {
                $model->content = ee($model->content);
            }
        });

    }

    public static function prepare_pdf($source, $rid)
    {
        $purl = 'files/reviews/' . $rid . '/';
        $file_full_pdf = $purl . 'pdf_' . $rid . '.pdf';
        $src_pdf = ROOT . $file_full_pdf;
        if (file_exists($src_pdf)) {
            @unlink($src_pdf);
        }
        $im = new \Imagick(ROOT . $source);
        $im->setImageFormat('pdf');
        $im->writeImage($src_pdf);
        return '/' . $file_full_pdf;
    }

    public static function fileInit($module, $id, $content, $what = 'temp')
    {
        $temp = 'files/' . $module . '/' . $what;
        $new = 'files/' . $module . '/' . $id;
        rename(ROOT . $temp, ROOT . $new);
        return str_replace($temp, $new, $content);
    }

    //public static function prepare_image($source, $purl)
    //{
    //    $dir = ROOT . $purl;
    //    if (!file_exists($dir)) {
    //        mkdir($dir, 0777);
    //    }
    //    $image = str_replace("https://atri-energo.ru/", "", $source);
    //    $image = str_replace("atri-energo.ru/", "", $image);
    //    $image = rawurldecode($image);
    //    $file_full = $image;
    //    $src = ROOT . $file_full;
    //    $info = pathinfo($src);
    //    $ext = $info['extension'];
    //    $name = $info['filename'];
    //    switch (mb_strtolower($ext)) {
    //        default:
    //            $img = imageCreateFromJpeg($src);
    //            break;
    //
    //        case 'png':
    //            $img = imageCreateFromPng($src);
    //            break;
    //
    //        case 'jpg':
    //            $img = imageCreateFromJpeg($src);
    //            break;
    //
    //        case 'gif':
    //            $img = imageCreateFromGif($src);
    //            break;
    //    }
    //    imageWebp($img, ROOT . $purl . '/' . $name . '.webp', 80);
    //    imagedestroy($img);
    //    $preview_array = array();
    //    $preview_array['source'] = $source;
    //    $preview_array['full'] = '/' . $image;
    //    $preview_array['full_webp'] = '/' . $purl . '/' . $name . '.webp';
    //    return $preview_array;
    //}

    public function categories()
    {
        return $this->belongsTo(\Boot\Models\Categories::class, "category");
    }

    public function organizations()
    {
        return $this->belongsTo(\Organizations\Model\Organizations::class, "oid");
    }

    public function getUrlAttribute()
    {
        if (!empty($this->category)) {
            $cat = new Categories;
            $url = '/reviews/' . $cat->getCategoryFullUrl($this->category) . 'view/' . $this->slug;
        } else {
            $url = '/reviews/view/' . $this->slug;
        }
        return $url;
    }
}