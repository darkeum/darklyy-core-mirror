<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Websocket class
*/

//подключаем основные константы 
require dirname(__FILE__) . '/../define.php';
header('Access-Control-Allow-Origin: *');
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';

use \Project\Model\Scales;
use \Scada\Scada;


$localsocket = 'tcp://atri-energo.ru:1234';
$user = 'tester01';
$message = 'test';

// соединяемся с локальным tcp-сервером
$instance = stream_socket_client($localsocket);
// отправляем сообщение
fwrite($instance, json_encode(['message_type' => 'scada', 'scada_id' => '3', 'message' => $message])  . "\n");


// Scada::collectScada(3);
// Scada::getScadaCollect(3);





// $types = ["Анкеры", "Болт", "Винт", "Гайка", "Гвоздь", "Гровер", "Заклепка", "Круг отрезной", "Ось мебельная", "Проволока", "Рым-крепеж", "Саморез", "Такелаж", "Шайба", "Шпилька", "Шплинт", "Шуруп"];
//         // $types = ["Анкеры"];
//         foreach($types as $key => $value){
            
//             $stand = json_decode(file_get_contents('https://ruscrep.ru/calcv2/task.php?types='.urlencode($value)), true);
//             foreach($stand as $key_stand => $value_stand){
//                 $dia = json_decode(file_get_contents('https://ruscrep.ru/calcv2/task.php?stand='.urlencode($value_stand).'&types='.urlencode($value)), true);
                
//                 foreach($dia as $key_dia => $value_dia){
//                     $dlina = json_decode(file_get_contents('https://ruscrep.ru/calcv2/task.php?stand='.urlencode($value_stand).'&dia='.urlencode($value_dia).'&types='.urlencode($value)), true);
//                     foreach($dlina as $key_dlina => $value_dlina){
//                         $material = json_decode(file_get_contents('https://ruscrep.ru/calcv2/task.php?stand='.urlencode($value_stand).'&dia='.urlencode($value_dia).'&dlina='.urlencode($value_dlina).'&types='.urlencode($value)), true);
//                         foreach($material as $key_material => $value_material){
//                             $data = json_decode(file_get_contents('https://ruscrep.ru/calcv2/task.php?stand='.urlencode($value_stand).'&dia='.urlencode($value_dia).'&material='.urlencode($value_material).'&dlina='.urlencode($value_dlina).'&types='.urlencode($value)), true);
//                             $pokr = [];
//                             foreach($data['pokr'] as $key_pokr => $value_pokr){
//                                 $pokr = json_decode($value_pokr, true);
//                             }
//                             $klp = [];
//                             foreach($data['klp'] as $key_klp => $value_klp){
//                                 $klp = json_decode($value_klp, true);
//                             }
//                             $item = [
//                                 'id' => $data['id'],
//                                 'massa' => $data['massa'],
//                                 'pokr' => $pokr,
//                                 'klp' => $klp,
//                             ];
//                             Scales::create([
//                                 'types' => $value, 
//                                 'stand' => $value_stand, 
//                                 'dia' => $value_dia, 
//                                 'dlina' => $value_dlina, 
//                                 'material' => $value_material, 
//                                 'item' => $item
//                             ]);
//                             // $res[$value][$value_stand][$value_dia][$value_dlina][$value_material] = [
//                             //     'id' => $data['id'],
//                             //     'massa' => $data['massa'],
//                             //     'pokr' => $pokr,
//                             //     'klp' => $klp,
//                             // ];
//                         }
//                     }
//                 }
//             }
           
//         }