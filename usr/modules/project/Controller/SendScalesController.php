<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Str Helper class
*/

namespace Project\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Categories\Categories;
use \Boot\Services\Request;
use \Boot\Support\Facades\Router;
use \Boot\System\Validation\Validator;
use \Order\Model\Order;
use \Order\Model\OrderHeat;
use \Project\Model\Scales;

class SendScalesController extends AbstractController
{
    public static function scales()
    {
        $result = [];
        if (isset($_GET['gettypes'])) {  
            $types = Scales::groupBy('types')->distinct()->orderBy('types', 'ASC')->get();           
            foreach($types as $key => $value){
                $result[] = $value->types;
            }    
        } elseif (isset($_GET['types']) && !isset($_GET['stand'])) {
            $stand = Scales::where('types', $_GET['types'])->groupBy('stand')->distinct()->orderBy('stand', 'ASC')->get();           
            foreach($stand as $key => $value){
                $result[] = $value->stand;
            } 
        } elseif (isset($_GET['types']) && isset($_GET['stand']) && !isset($_GET['dia'])) {
            $dia = Scales::where('types', $_GET['types'])->where('stand', $_GET['stand'])->groupBy('dia')->distinct()->orderBy('dia', 'ASC')->get();           
            foreach($dia as $key => $value){
                $result[] = $value->dia;
            }           
        } elseif (isset($_GET['types']) && isset($_GET['stand']) && isset($_GET['dia']) && !isset($_GET['dlina'])) {
            $dlina = Scales::where('types', $_GET['types'])->where('stand', $_GET['stand'])->where('dia', $_GET['dia'])->groupBy('dlina')->distinct()->orderBy('dlina', 'ASC')->get();           
            foreach($dlina as $key => $value){
                $result[] = $value->dlina;
            }     
        } elseif (isset($_GET['types']) && isset($_GET['stand']) && isset($_GET['dia']) && isset($_GET['dlina']) && !isset($_GET['material'])) {
            $material = Scales::where('types', $_GET['types'])->where('stand', $_GET['stand'])->where('dia', $_GET['dia'])->where('dlina', $_GET['dlina'])->groupBy('material')->distinct()->orderBy('material', 'ASC')->get();           
            foreach($material as $key => $value){
                $result[] = $value->material;
            }     
        } elseif (isset($_GET['types']) && isset($_GET['stand']) && isset($_GET['dia']) && isset($_GET['dlina']) && isset($_GET['material'])) {
            $data = Scales::where('types', $_GET['types'])->where('stand', $_GET['stand'])->where('dia', $_GET['dia'])->where('dlina', $_GET['dlina'])->where('material', $_GET['material'])->first();           
            $result = $data->item;
            // $result = $types[$_GET['types']][$_GET['stand']][$_GET['dia']][$_GET['dlina']][$_GET['material']];
            
        } elseif (isset($_GET['tovid'])){
            $result = config('app.url').'/files/no_photo.jpg';
            //exit;
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public static function scales_download()
    {
        print_r($_GET);
        
    }

   
}
