<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Request\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Boot\Controller\AbstractController;
use Boot\Models\Attach;
use Request\Model\Request as ModelRequest;
use Boot\Support\Facades\Auth;
use Catalog\Models\Catalog;
use Catalog_v2\Model\CatalogManufacture;
use Object\Model;
use Organizations\Model\Organizations;

class RequestController extends AbstractController
{
    static function index()
    {
        return view('request::manager.index');
    }

    public static function view($id)
    {   
        $returnArray=array();

        $data=ModelRequest::find($id);

        $files=Attach::where("pid",'=',$id)->where("module",'=','request')->get();
        $returnArray['files'] = $files;

        $quertAttach = Attach::where("pid",'=',$id)->where("module",'=','request_catalog')->get();
        $count_query = count($quertAttach);
        $returnArray['count_query'] = $count_query;

        switch($data->type)
        {
            case 1:            
                $title = "Свободная";
                break;
            case 2:
                $title = "Атри-проект";
                break;
            case 3:
                $title = "Расчет теплообменника";
                break;
            case 4:
                $title = "";
                break;
            case 5:
                $title = "";

                $manafacture = CatalogManufacture::find($data->request_data['manufacture']);

                $catalog = Catalog::find($data->request_data['cid']);

                if($data->request_data['object_id']!=null)
                    $obj = Object::find($data->request_data['object_id']) ;
                else
                    $obj = "Отсутствует";

                $org = Organizations::find($data->request_data['oid']);

                $returnArray['manafacture'] = $manafacture;
                $returnArray['catalog'] = $catalog;
                $returnArray['obj'] = $obj;
                $returnArray['org'] = $org->name;
                $returnArray['request_data'] = $data->request_data;
                break;
            case 6:
                $title = "Расчет насосной станции";

                $data_rusult = $data->result[0]['series']['wp'] ??  $data->result[0]['series']['wpb']  ??  $data->result[0]['series']['wpbs'];
                $station    =   array();
                $electric   =   array();

                foreach($data_rusult['station'] as $item)
                {
                    if($item['tm_cid'] == null)
                        continue;

                    if(isset($data_rusult['station_price']['priceArray'][$item['tm_cid']]))
                        $price = $data_rusult['station_price']['priceArray'][$item['tm_cid']];
                    else 
                        $price = 0;
                    $station[] = array(
                    'tm_cid' => $item['tm_cid'],
                    'tm_count' => $item['tm_count'],
                    'tm_sku' => $item['tm_sku'],
                    'tm_title' => $item['tm_title'],
                    'tm_price' => $price,
                    'total_price' => (int)$item['tm_count'] * (int)$price,
                    );
                }
                foreach($data_rusult['electric'] as $item)
                {
                    if($item['tm_cid'] == null)
                    continue;
                    
                    if(isset($data_rusult['electric_price']['priceArray'][$item['tm_cid']]))
                        $price = $data_rusult['electric_price']['priceArray'][$item['tm_cid']];
                    else 
                        $price = 0;
                    
                    $electric[] = array(
                    'tm_cid' => $item['tm_cid'],
                    'tm_count' => $item['tm_count'],
                    'tm_sku' => $item['tm_sku'],
                    'tm_title' => $item['tm_title'],
                    'tm_price' => $price,
                    'total_price' => (int)$item['tm_count'] * (int)$price,
                    );
                }

                
                $returnArray['electric'] = $electric;
                $returnArray['station'] = $station;
                break;    
            default:
                $title = "Отсутствует";
                break;
        }

        $returnArray['title'] = $title;
        $returnArray['id'] = $id;
        $returnArray['uid_exec'] = $data->uid_exec;
        $returnArray['status'] = $data->status;
        $returnArray['type'] = $data->type;
        $returnArray['title_user'] = $data->title_user;
        $returnArray['uid_dir'] = $data->uid_dir;

        return view('request::manager.view',$returnArray);
        
    }

    public static function add()
    {
        $select_obj=static::getSelect([
            "name" => "object_id",
            "ajaxUrl" => "getObjectToSelect",
            "select_class" => "object_select",
            "select_theme" => 'default'
        ]);
        return view('request::manager.create',[
            'select_obj' => $select_obj
        ]);
    }

    public static function creating(Request $data)
    {   

        $model = new ModelRequest();
        $arr=array(
            'title' => $data['title'],
            'request' => $data['request'],
            'object_id' => $data['object_id'],
            'type' => $data['type'],
            'uid_dir' => Auth::id(),
            'date' => date("Y-m-d H:i:s")
        );

        $model->fill($arr);
        $model->save();
        return redirect()->back();
    }

    private static function getSelect($request)
    {
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => $request["name"],
            "ajaxUrl" => $request["ajaxUrl"],
            "id" => $request["id"] ?? null,
            "return" => true,
            "bootstrap" => true,
        ];
        if (isset($request["select_class"])) {
            $oSelectSettingJS["select_class"] = $request["select_class"];
        }
        if (isset($request["select_theme"])) {
            $oSelectSettingJS["select_theme"] = $request["select_theme"];
        }
        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }

}
