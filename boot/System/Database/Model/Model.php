<?php 

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Model base abstract class
*/

namespace Boot\System\Database\Model;

use \Boot\Support\Str;
use \Boot\System\Database\Model\Builder;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\HasResourceKeyTrait;

abstract class Model extends \Illuminate\Database\Eloquent\Model{

    use HashIdTrait;
    use HasResourceKeyTrait;

    protected $table_no_prefix;

    public function getTable(){
        if(empty($this->table)){
            $this->table_no_prefix = Str::snake(class_basename($this));
            $this->table = $this->table_no_prefix;
        } else {
            if(empty($this->table_no_prefix)){
                $this->table_no_prefix = str_replace('JMY'."_", "", $this->table);
            }
        }
        return $this->table;
    }

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    public function setLog($model, $request = []){
        //["pid" => "", "comment" => ""]
        $pid = $model->id;
        $type = empty($request["type"]) ? 'info' : $request["type"];
        $module = empty($request["module"]) ? $this->table_no_prefix : $request["module"];
        $comment = $request["comment"];
        //refresh()
        $data_json = $model->toJson();
        $e_comment = $comment." Data: ".$data_json;
        
        setLog($type, $pid, $module, $e_comment);
    }

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    protected function asJson($value){
        /**
         * Alter Cast
         * Default is altering unicode
         */
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }


}