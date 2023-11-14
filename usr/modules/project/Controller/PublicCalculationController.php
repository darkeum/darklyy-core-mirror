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
use \Order\Models\Order;
use \Boot\Models\GeoRegion;
use \Boot\Models\GeoCity;
use \Boot\Models\GeoTso;
use \Order\Models\OrderHeat;
use \Boot\Support\Facades\Auth;

class PublicCalculationController extends AbstractController{

    public static function index(){ 
        $title = 'Расчеты и подборы';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => $title, 'url' => router('project.calculation.index')],
        ];
        display('project/calculation/index.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function heat($route = null){ 
        $title = 'Расчет теплообменника';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.heat')],
        ];
        display('project/calculation/heat.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function pump($route = null){ 
        $title = 'Расчет насосного оборудования';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.pump')],
        ];
        display('project/calculation/pump.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function pump_station($route = null){ 
        $title = 'Расчет насосной станции';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.pump')],
        ];
        display('project/calculation/pump_station.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function metering_station($route = null){ 
        $region = GeoRegion::where('status', 1)->get();
        $city = GeoCity::get();
        $tso = GeoTso::get();
        $title = 'Подбор узла учета';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.metering_station')],
        ];
        $uid = Auth::id();
        if($uid == 1) {
            $t = 'project/calculation/metering_station_dev.html.twig';
        } else {
            $t = 'project/calculation/metering_station.html.twig';
        }
        display($t, [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'region' => $region,
            'city' => $city,
            'tso' => $tso,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function block($route = null){ 
        $title = 'Расчет блочного теплового пункта';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.block')],
        ];
        display('project/calculation/block.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }

    public static function valve($route = null){ 
        $title = 'Подбор регулирующего клапана';  
        $breadcrumbs  = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Проектирование', 'url' => router('project.index')],
            ['name' => 'Расчеты и подборы', 'url' => router('project.calculation.index')],
            ['name' =>  $title, 'url' => router('project.calculation.valve')],
        ];
        display('project/calculation/valve.html.twig', [
            'breadcrumbs' =>  $breadcrumbs,
            'title' => $title,
            'page_title' => $title,
            'page_description' => $title,
        ]);
    }




    public static function send($route = null){ 
        $error_output = '';
        $success_output = '';
        $validator = new Validator;
        $validation = $validator->validate([
            'g-recaptcha-response'  => 'required|recaptcha',
            'email'                 => 'required|email',
            'phone'                 => 'required|phone_dd',
            'name'                  => 'required|name_dd',           
        ]);
        if ($validation->fails()) {
            $type = 'error';
            $errors = $validation->errors();
            $error_output = $errors->firstOfAll();
        } else {
            $data = $validation->getValidData();
            $dataArray['type'] = 'heat';
            $dataArray['email'] = $data['email'];
            $dataArray['surname'] = $data['name']['surname'];
            $dataArray['name'] = $data['name']['name'];
            $dataArray['ochestvo'] = $data['name']['patronymic'];
            $dataArray['fio_source'] = $data['name'];
            $dataArray['phone'] = $data['phone']['phone'];
            $dataArray['phone_source'] = $data['phone'];
            $dataArray['message'] = $_POST['message'] ?? null;
            $dataArray['object_name'] = $_POST['object_name'] ?? null;
            $dataArray['org_name'] = $_POST['org_name'] ?? null;
            $dataArray['org_inn'] = $_POST['org_inn'] ?? null;

            $type = 'success';
            $order = Order::create($dataArray);
            $hc = new \Calculation\HeatCalculation();
            $result = $hc->getHeatOrder($_POST);
            OrderHeat::create(['order_id' => $order->id, 'request_id' => $result['requestID']]);
            $success_output = 'Расчет теплообменного оборудования отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.';
        }
        $output = array(
            'type'        =>    $type,
            'error'     =>  $error_output,
            'success'   =>  $success_output
        );
        echo json_encode($output);
        exit;	
    }

}