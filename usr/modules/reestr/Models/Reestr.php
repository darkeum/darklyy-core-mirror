<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Reestr\Models;

use Sync\Sync;
use Boot\Models\Model;
use Boot\Models\Relations;
use Reestr\Models\ReestrType;
use Boot\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reestr extends Model
{
    use SoftDeletes;
    protected $fillable = ["type", "type_doc", "numb", "other_numb",  "uid", "oid", "org", "object", "a_doc", "a_scan", "sign", "scan", "doc",  "comment", "date", "date_view", "date_create", "auto", "spec_numb_counter", "status"];

    public function reestr_type()
    {
        return $this->belongsTo(\Reestr\Models\ReestrType::class, "type");
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->date)) {
                $model->date = date("d.m.Y");
                $parseDate = explode(' ', $model->date);
                $subDate = explode('.', $parseDate[0]);
                if (isset($parseDate[1])) {
                    $subTime = explode(':', $parseDate[1]);
                } else {
                    $subTime[0] = 12;
                    $subTime[1] = 0;
                }
                $model->date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
            }
            $model->date_view = $model->date_view ?? date('Y.m.d', $model->date);
            $model->numb = self::numb_counter($model);
            $model->date_create = time();
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });

        static::created(function ($model) {
            static::sync_register($model, 1);
        });

        static::updated(function ($model) {
            if ($model->wasChanged("numb") || $model->wasChanged("other_numb") || $model->wasChanged("date_view") || $model->wasChanged("oid") || $model->wasChanged("org") || $model->wasChanged("type_doc")) {
                static::sync_register($model, 2);
            }
        });
    }

    static public function sync_register($model, $type)
    {
        $sync = new Sync;
        $sync->syncRegister('reestr', $model->id, $type, '0');
    }

    static public function numb_counter($model)
    {
        $reestr = static::where("org", $model->org)->orderByDesc('id')->first();
        return empty($reestr) ? 1 : $reestr->numb + 1;
    }

    static function searchByModule($module_out, $pid_out)
    {
        $item = Relations::where("module_out", $module_out)
            ->where("pid_out", $pid_out)
            ->where("module_in", "reestr")
            ->first();
        if (!empty($item)) {
            return \Reestr\Models\Reestr::find($item->pid_in);
        }
        return null;
    }

    static function getTypeDoc()
    {
        return [
            "out" => "Покупатели",
            "in" => "Поставщики",
        ];
    }

    public function getTypeDocViewAttribute()
    {
        $type = static::getTypeDoc();
        return $type[$this->type_doc] ?? $this->type_doc;
    }

    public static function getReestrNumb($id, $numb)
    {
        $r = ReestrType::find($id);
        return $r->short . '-' . $numb;
    }

    public function docs()
    {
        return $this->hasMany(ReestrDoc::class, 'reestr_id');
    }
}
