<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

//подключаем основные константы 
require '../../define.php';
header('Access-Control-Allow-Origin: *');
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';

use \Boot\Support\Facades\Config;
use \Boot\Services\Request;

function commandExec($command){
    ob_start();
    system($command . " 2>&1", $result);

    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}

 function validateGithubWebhook($known_token)
    {
        $request = new Request();
        if (($signature = $request->headers->get('X-Hub-Signature')) == null) {
            throw new \Exception('Header not set');
        }

        $signature_parts = explode('=', $signature);

        if (count($signature_parts) != 2) {
            throw new \Exception('signature has invalid format');
        }

        $known_signature = hash_hmac('sha1', $request->getContent(), $known_token);

        if (! hash_equals($known_signature, $signature_parts[1])) {
            throw new \Exception('Could not verify request signature ' . $signature_parts[1]);
            // file_put_contents("error.json",'Could not verify request signature ' . $signature_parts[1], FILE_APPEND);
        } else {
            // file_put_contents("success.json",'OK' . $signature_parts[1], FILE_APPEND);
            // file_put_contents("json.json", $request->getContent(), FILE_APPEND);
            $rrr= json_decode( $request->getContent());
            if( $rrr->ref == 'refs/heads/main'){
                commandExec('git pull');
            }
           
        }
    }




        validateGithubWebhook(Config::get('services.github_webhook_secret'));

      
// $json_string = json_encode($_POST);



// file_put_contents("post.json", $json_string, FILE_APPEND);

// $json_string = json_encode($_GET);

// file_put_contents("get.json", $json_string, FILE_APPEND);

// $json_string = json_encode($_SERVER);

// file_put_contents("server.json", $json_string, FILE_APPEND);

// $json_string = json_encode($_REQUEST);

// file_put_contents("request.json", $json_string, FILE_APPEND);