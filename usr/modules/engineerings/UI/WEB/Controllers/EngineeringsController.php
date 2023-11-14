<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Engineerings\UI\WEB\Controllers;


use Boot\Support\Facades\DB;
use Boot\Controller\AbstractController;
use Boot\System\Http\Request;

class EngineeringsController extends AbstractController
{
    static function index()
    {
        return redirect()->route('admin.engineerings.register');
        return view('engineerings::manager.index');
    }

    static function register()
    {
        return view('engineerings::manager.register.register');
    }

    static function registerCreate($id = null)
    {
        if($id){
            $r = DB::table("engineering_register")->find($id);
            $numb = $r->numb;
            $section = json_decode($r->section, true);
            $object = $r->object;
            $date = $r->date;
            $uid = json_decode($r->uid, true);
            $status = (int)$r->status;
            $col_size = 6;
            $inp_size = 10;
        } else {
            $numb = "";
            $section = [];
            $object = null;
            $date = date("Y-m-d");
            $uid = null;
            $status = -1;
            $col_size = 12;
            $inp_size = 8;
        }



        return view('engineerings::manager.register.create',[
            'id' => $id,
            'input_size' => $inp_size,
            'section_type' => static::section_type(),
            'status_type' => static::get_status(),
            'section' => $section,
            'date' => $date,
            'numb' => $numb,
            'status' => $status,
            'select_obj' => static::getSelect([
                "name" => "select_Obj",
                "ajaxUrl" => "getObjectToSelect",
                "select_class" => "select_obj",
            ]),
            'select_usr' => static::getSelect([
                "name" => "select_usr[]",
                "ajaxUrl" => "getUsersAllToSelect",
                "select_class" => "select_usr",
                'multiple' => 'multiple',
            ]),
        ]);
    }

    static function registerCreating(Request $data)
    {

        $id = isset($data["id"]) ? htmlspecialchars($data["id"], ENT_QUOTES) : false;
        $numb = isset($data["numb"]) ? htmlspecialchars($data["numb"], ENT_QUOTES) : false;
        $section = isset($data["section"]) ? $data["section"] : [];
        $section_json = json_encode($section, JSON_UNESCAPED_UNICODE);
        $object = isset($data["select_Obj"]) ? htmlspecialchars($data["select_Obj"], ENT_QUOTES) : 'null';
        $date = isset($data["date"]) ? htmlspecialchars($data["date"], ENT_QUOTES) : false;
        $uid = isset($data["select_usr"]) ? $data["select_usr"] : [];
        $uid_json = json_encode($uid, JSON_UNESCAPED_UNICODE);
        $status = isset($data["status"]) ? htmlspecialchars($data["status"], ENT_QUOTES) : false;
        
        dd($data);

        if($id){
            // $db->query("UPDATE ".DB_PREFIX."_engineering_register SET `numb` = '".$numb."'
            // , `section` = '".$section_json."', `object` = ".$object."
            // , `date` = '".$date."', `uid` = '".$uid_json."'
            // , `status` = '".$status."' WHERE id = '".$id."' LIMIT 1");
        
        $array_update = array(

            "numb" => $numb,
            "section" => $section_json,
            "object" => $object,
            "date" => $date,
            "uid" => $uid_json,
            "status" => $status,

        );

        DB::table("engineering_register")->find($id)->update($array_update);
        
        } else {
            $db->query("INSERT INTO ".DB_PREFIX."_engineering_register (`numb`, `section`, `object`, `date`, `uid`, `status`) VALUES ('".$numb."', '".$section_json."', ".$object.", '".$date."', '".$uid_json."', '".$status."')");
            $id = $db->insert_id();
            echo $id;
        }
        
    }

    private static function getSelect($request)
    {
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => $request["name"],
            "ajaxUrl" => $request["ajaxUrl"],
            "id" => $request["id"] ?? null,
            "multiple" => $request["multiple"] ?? null,
            "return" => true,
            "bootstrap" => 'default',
        ];

        if (isset($request["select_class"])) {
            $oSelectSettingJS["select_class"] = $request["select_class"];
        }

        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }

    static function section_type(){
        $type = [
            1 => "УУ", "ТМ", "ВК", "ОВ", "ТС", "НК", "АС", "Э", "АТС", "ЭОМ", "ЭО", "ЭМ", "КР", "ГСВ", "НВ", "АГСВ", "АСКУЭ", "ГСН",
        ];
        return $type;
    }
    
    static function get_status(){
        $status = [
            1 => "Первый статус",
        ];
        return $status;
    }
    
}