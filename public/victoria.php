<?php

header("Access-Control-Allow-Origin: *");

//подключаем основные константы 
require dirname(__FILE__) . '/../define.php';

//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';

$action = $_GET["action"] ?? false;
if(!$action) throw new \Exception("Action not found!");

$VictoriaSite = new \VictoriaSite;
switch($action){
    default:
        $id = $_GET["id"] ?? false;
        if(!$id) throw new \Exception("ID not found!");
        $token = $_GET["token"] ?? false;
        if(!$token) throw new \Exception("Token not found!");
        $VictoriaSite->id = $id;
        $VictoriaSite->url_add = "/api/app/sales.add_detail?id={$id}&token={$token}";
        $sales = \Sales\Model\Sales::findOrFail($id);
        $VictoriaSite->oid = $sales->oid;
        echo $VictoriaSite->popup();
    break;
    case "view_min":
        $id = $_GET["id"];
        // if(!$id) throw new \Exception("ID not found!");
        $token = $_GET["token"] ?? false;
        if(!$token) throw new \Exception("Token not found!");

        $VictoriaSite->search_value = $_GET["search_value"] ?? "";
        $VictoriaSite->id = $id;
        $VictoriaSite->transfer_to_document_disabled = true;
        $VictoriaSite->local_script = <<<HTML
            document.domain = "atri-energo.ru";
			\$victoria.add_basket = async function(cid){
				const id = this.id;
                await opener.\$Victoria.changeItem(id, cid);
				window.close();
			}
HTML;
        echo $VictoriaSite->popup();
    break;
    case "load_list":
        $id = $_POST["id"] ?? false;
        if(!$id) throw new \Exception("ID not found!");
        $sales = \Sales\Model\Sales::findOrFail($id);
        $VictoriaSite->oid = $sales->oid;
        $data = $VictoriaSite->grid_items_body($_POST);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    break;
    case "filter_data":
        $data = $VictoriaSite->filter($_POST);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    break;
    case "get_item":
        $id = $_POST["main_id"] ?? false;
        if(!$id) throw new \Exception("ID not found!");
        $sales = \Sales\Model\Sales::findOrFail($id);
        $VictoriaSite->oid = $sales->oid;
        $data = $VictoriaSite->grid_items_basket_body($_POST);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    break;
}


