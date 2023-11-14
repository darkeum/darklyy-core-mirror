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
use \Boot\System\Validation\Validator;
use \Order\Models\Order;
use \Order\Models\OrderHeat;
use \Order\Models\OrderPump;
use \Order\Models\OrderPumpStation;
use \Order\Models\OrderMeteringStation;
use \Order\Models\OrderBlock;
use \Order\Models\OrderValve;

class SendCalculationController extends AbstractController{

    public static function block($route = null){ 
       $send = static::send('block', 'расчет блочного теплового пункта');
       $output = $send;
       if($send['type'] == 'success'){
            OrderBlock::create([
                'order_id' => $send['order_id'], 
                'block_data' => (!empty($_POST['sub']) ? json_decode($_POST['sub'], true) : null),
            ]);
            $output['success'] = 'Расчет блочного теплового пункта отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 3-х рабочих дней.';
       }  
       echo json_encode($output);
       exit;	
    }

    public static function metering_station($route = null){ 
        $send = static::send('metering_station', 'расчет узла учета');
        $output = $send;
        if($send['type'] == 'success'){
            OrderMeteringStation::create([
                 'order_id' => $send['order_id'], 
                 'metering_data' => (!empty($_POST['sub']) ? json_decode($_POST['sub'], true) : null),
             ]);
             $output['success'] = 'Расчет узла учета отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 3-х рабочих дней.';
        }  
        echo json_encode($output);
        exit;	
     }

    public static function pump($route = null){ 
        $send = static::send('pump', 'расчет насосного оборудования');
        $output = $send;
        if($send['type'] == 'success'){            
            OrderPump::create([
                'order_id' => $send['order_id'], 
                'pressure' => $_POST['pressure'] ?? null,
                'qPump' => $_POST['qPump'] ?? null,
                'type' => $_POST['type'] ?? null,
                'r1' => $_POST['r1'] ?? null,
                'r2' => $_POST['r2'] ?? null,
               ]);
            $output['success'] = 'Расчет насосного оборудования отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.';
        }  
        echo json_encode($output);
        exit;	
     }

    public static function pump_station($route = null){ 
        $send = static::send('pump_station', 'расчет насосной станции');
        $output = $send;
        if($send['type'] == 'success'){            
            OrderPumpStation::create([
                'order_id' => $send['order_id'], 
                'sum' => $_POST['sum'] ?? null,
                'sumr' => $_POST['sumr'] ?? null,
                'napor' => $_POST['napor'] ?? null,
                'rashod' => $_POST['rashod'] ?? null,
                'tocha' => $_POST['tocha'] ?? null,
                'r1' => $_POST['r1'] ?? null,
                'r2' => $_POST['r2'] ?? null,
               ]);
            $output['success'] = 'Расчет насосной станции отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.';
        }  
        echo json_encode($output);
        exit;	
     }

    public static function heat($route = null){ 
        $send = static::send('heat', 'расчет теплообменного оборудования');
        $output = $send;
        if($send['type'] == 'success'){
            $hc = new \Calculation\HeatCalculation();
            $result = $hc->getHeatOrder($_POST);
            OrderHeat::create(['order_id' => $send['order_id'] , 'request_id' => $result['requestID']]);
            $output['success'] = 'Расчет теплообменного оборудования отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.';
        }     
        echo json_encode($output);
        exit;	 
    }  

    public static function valve($route = null){ 
        $send = static::send('pump_station', 'расчет регулирующего клапана');
        $output = $send;
        if($send['type'] == 'success'){            
            OrderValve::create([
                'order_id' => $send['order_id'], 
                'nagr' => $_POST['nagr'] ?? null,
                't1' => $_POST['t1'] ?? null,
                't2' => $_POST['t2'] ?? null,
                'q' => $_POST['q'] ?? null,
                'p1' => $_POST['p1'] ?? null,
                'p2' => $_POST['p2'] ?? null,
                'tmax' => $_POST['tmax'] ?? null,
                'pressure_drop' => $_POST['pressure_drop'] ?? null,
               ]);
            $output['success'] = 'Расчет регулирующего клапана отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.';
        }  
        echo json_encode($output);
        exit;	
     }

    private static function send($type, $title){ 
        $error_output = '';
        $success_output = '';
        $order_id = '';
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
            $dataArray['type'] = $type;
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
            $order_id = $order->id;

            $attr = array();
		    $attr['email'] = $data['email'];
		    $attr['name'] = $data['name']['result'];
		    $attr['title'] = 'Ваша заявка на расчет принята';		 
		    $attr['text'] = 'Здравствуйте, <b>' . $attr['name'].'!</b> <br> Ваша заявка по расчету '. $title.' <b>№'.$order_id.'</b> принята. Результаты выполнения будут высланы вам на почту. У вас остались вопросы? Связавшись с нами по  телефону +7(3412)908-123';	
            $attr['url'] = config('app.url').'/feedback';
		    $attr['url_text'] = 'Контакты';	   
		    sendEmailByData($attr);           
        }
        $output = array(
            'type'      =>  $type,
            'error'     =>  $error_output,
            'success'   =>  $success_output,
            'order_id'   => $order_id,
        );
        return $output;
    }

}