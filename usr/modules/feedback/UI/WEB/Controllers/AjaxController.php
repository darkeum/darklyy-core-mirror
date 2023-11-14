<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Feedback public controller
*/

namespace Feedback\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Boot\System\Validation\Validator;
use \Boot\System\Http\Request;

use \Order\Models\Order;

class AjaxController extends AbstractController
{

    public function send(Request $request)
    {
        if ($request->ajax()) {

            $error_output = '';
            $success_output = '';

            $validator = new Validator;
            $validation = $validator->validate([
                'g-recaptcha-response' => 'required|recaptcha',
                'email' => 'required|email',
                'phone' => 'required|phone_dd',
                'name' => 'required|name_dd',
                'message' => 'required',
            ]);
            
            if ($validation->fails()) {
                $type = 'error';
                $errors = $validation->errors();
                $error_output = $errors->firstOfAll();

            } else {

                $data = $validation->getValidData();
                $dataArray['type'] = 'feedback';
                $dataArray['email'] = $data['email'];
                $dataArray['surname'] = $data['name']['surname'];
                $dataArray['name'] = $data['name']['name'];
                $dataArray['ochestvo'] = $data['name']['patronymic'];
                $dataArray['fio_source'] = $data['name'];
                $dataArray['phone'] = $data['phone']['phone'];
                $dataArray['phone_source'] = $data['phone'];
                $dataArray['message'] = $data['message'];
                $type = 'success';
                // Order::create($dataArray);
                $success_output = 'Спасибо за обращение! Ваше сообщение успешно отправлено, в ближайшее время с вами свяжутся наши сотрудники.';
                
            }

            $output = array(
                'type' => $type,
                'error' => $error_output,
                'success' => $success_output
            );

            return json_encode($output);
        }
    }
}