<?php

namespace Organizations\Model;

use \Boot\Models\UsersOrg;
use Reestr\Models\Reestr;
use \Boot\Support\Facades\Data;

class Organizations extends \Model\Model{
    protected $fillable = ["type", "name", "name_full", "yaddress", "maddress", "address_source", "inn", "kpp", "ogrn", "post", "director", "director_detail", "dey", "phone", "email", "form", "reestr_id", "description", "client_type", "default_responsible", "organizations_bank_default", "status"];

    protected $casts = [
        'director_detail' => 'object',
    ];

    static public function check_org($request){
        $result = ["check" => false];
        $inn = empty($request["inn"]) ? false : $request["inn"];
        if($inn){
            $count = self::where("inn", $inn)->count();
            $result["check"] = !($count > 0);
        }
        return $result;
    }

    public function banks(){
        return $this->hasMany(OrganizationsBank::class, "oid");
    }

    public function users(){
        // return $this->belongsToMany(UsersOrg::class, "users_org_relation", 'org', 'uid');
        return $this->hasMany(UsersOrg::class, "oid");
    }

    public function registers(){
        return $this->hasMany(Reestr::class, "oid");
    }

    public function org(){
        return $this->hasOne(\Organizations\Model\Org::class, "oid");
    }

    public static function boot(){
        parent::boot();

        static::creating(function($model){
            if(!empty($model->director)){
                $model->director_detail = Data::name($model->director);
            }
        });

        static::created(function($model){
            self::sync_register($model, 1);
        });

        static::updated(function($model){
            self::sync_register($model, 2);
        });

        static::updating(function($model) {
            if($model->original['director'] != $model->attributes['director'])   {
                if(!empty($model->director)){
                    $model->director_detail = Data::name($model->director);
                } else {
                    $model->director_detail = null;
                }
            }
        });

        static::saved(function($model){
            if(!empty($model->org->id)){
                \Organizations\Model\Org::cache_delete($model->org->id);
            }
        });

    }

    public static function sync_register($model, $type){
        $sync = new \Sync\Sync;
        $sync->syncRegister('organizations', $model->id, $type, '0');
    }

    static function getClientType(){
        return [
            1 => "Неизвестно",
            2 => "Активный",
            3 => "Потерянный",
            4 => "Спящий",
            5 => "Одноразовый",
            6 => "Ликвидированный",
            7 => "Новый",
        ];
    }

    public function getClientTypeViewAttribute(){
        $type = static::getClientType();
        return $type[ $this->client_type ] ?? $this->client_type;
    }

    static function getType(){
        return [
            "LEGAL" => "Юридическое лицо",
            "INDIVIDUAL" => "Индивидуальный предприниматель",
        ];
    }

    public function getTypeViewAttribute(){
        $type = static::getType();
        return $type[ $this->type ] ?? $this->type;
    }
}