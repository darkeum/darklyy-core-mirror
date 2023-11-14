<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

use Requests\Models\Requests;

class Attach extends Model
{
    protected $fillable = ["name", "url", "pid", "module", "downloads", "daemon", "class_name", "access"];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            \Boot\Jobs\AttachJob::dispatch();
        });

        static::deleting(function ($model) {
            if (file_exists($model->url)) unlink($model->url);
        });
    }

    static function save_file(\Illuminate\Http\UploadedFile $file, $module, $pid, $folder_module = null, $folder_id = null)
    {
        $patch = "files/" . (is_null($folder_module) ? $module : $folder_module) . "/" . (is_null($folder_id) ? $pid : $folder_id);
        if (!file_exists(ROOT . $patch)) {
            mkdir(ROOT . $patch, 0777, true);
        }
        $name = $file->getClientOriginalName();
        $hashName = $file->hashName();
        $url = $patch . "/" . $hashName;
        $file->move(ROOT . $patch, $hashName);

        return static::create([
            "name" => $name,
            "url" => $url,
            "pid" => $pid,
            "module" => $module,
        ]);
    }

    //public function requests()
    //{
    //    return $this->belongsTo(Requests::class);
    //}
}
