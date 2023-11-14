<?php

use Logistics\CarParkTemplate;

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/logistics/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
    default:
        // logistics_detail();
        $LogisticsDetailTemp = new \Logistics\LogisticsDetailTemp;
        $LogisticsDetailTemp->view();
    break;
    case "detail_min":
        $LogisticsDetailTemp = new \Logistics\LogisticsDetailTemp;
        $LogisticsDetailTemp->view_clear();
    break;
    case "update_info":
        // echo logistics_detail_info();
        $LogisticsDetailTemp = new \Logistics\LogisticsDetailTemp;
        echo $LogisticsDetailTemp->detail_info();
    break;
    case "update_list":
        // echo logistics_detail_list();
        $request = $_POST;
        $LogisticsDetailTemp = new \Logistics\LogisticsDetailTemp;
        $LogisticsDetailTemp->request = $request;
        echo $LogisticsDetailTemp->detail_list();
    break;
    case "update_field_detail":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $field = isset($_POST["field"]) ? $_POST["field"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : "";
        if($id && $field){
            \Logistics\Model\LogisticsDetail::findOrFail($id)->update([$field => $value]);
        }
    break;
    case "x_update_field_detail":
        $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
        $field = isset($_POST["name"]) ? $_POST["name"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : "";
        if($id && $field){
            \Logistics\Model\LogisticsDetail::findOrFail($id)->update([$field => $value]);
        }
    break;
    case "detail_delete":
        $id = $url[4];
        \Logistics\Model\LogisticsDetail::destroy($id);
    break;
    case "mas_detail_delete":
        $request = $_POST;
        $id = $request["id"];
        \Logistics\Model\LogisticsDetail::destroy($id);
    break;
    case "mas_transport_companies":
        $request = $_POST;
        $id = isset($request["id"]) ? $request["id"] : false;
        $transport_companies = isset($request["transport_companies"]) ? $request["transport_companies"] : false;
        if($id && $transport_companies){
            foreach($id as $i){
                \Logistics\Model\LogisticsDetail::findOrFail($i)->update(["transport_companies" => $transport_companies]);
            }
        }
    break;
    case "download_supply_order":
        $id = $url[4];
        download_supply_order($id);
    break;
    case "auto_add":
        $data = \Logistics\Support\LogisticsDetailSupport::auto_add();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    break;
    case "notif_send":
        $id = $url[4];
        logistics_notif_send($id, $_POST);
    break;
    case "supply_order_link":
        $id = $url[4];
        supply_order_link($id);
    break;
    case "logistics_driver":
        switch(isset($url[4]) ? $url[4] : null){
            case "add":
                logistics_driver_add($_POST);
            break;
            case "detail":
                $id = $url[5];
                $LogisticsDriverDetailTemp = new \Logistics\LogisticsDriverDetailTemp;
                $LogisticsDriverDetailTemp->driver = $id;
                $LogisticsDriverDetailTemp->view();
                // logistics_driver_detail($id);
            break;
            case "detail_min":
                $id = $url[5];
                $LogisticsDriverDetailTemp = new \Logistics\LogisticsDriverDetailTemp;
                $LogisticsDriverDetailTemp->driver = $id;
                $LogisticsDriverDetailTemp->view_clear();
            break;
            case "update_info":
                $id = $url[5];
                $LogisticsDriverDetailTemp = new \Logistics\LogisticsDriverDetailTemp;
                $LogisticsDriverDetailTemp->driver = $id;
                echo $LogisticsDriverDetailTemp->detail_info();
                // echo logistics_driver_detail_info($id);
            break;
            case "update_list":
                $id = $url[5];
                $LogisticsDriverDetailTemp = new \Logistics\LogisticsDriverDetailTemp;
                $LogisticsDriverDetailTemp->driver = $id;
                echo $LogisticsDriverDetailTemp->detail_list();
                // echo logistics_driver_detail_list($id);
            break;
            case "pick_up":
                logistics_driver_pick_up($_POST);
            break;
            case "x_update_field_detail":
                $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
                $field = isset($_POST["name"]) ? $_POST["name"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : "";
                if($id && $field){
                    \Logistics\Model\LogisticsDriverDetail::findOrFail($id)->update([$field => $value]);
                }
            break;
            case "change_driver":
                $request = $_POST;
                $id = $request["id"] ?? false;
                $current_driver = $request["current_driver"] ?? false;
                $driver = $request["driver"] ?? false;
                if($id && $driver && $current_driver){
                    $query = $db->query("SELECT ldd.* 
                    FROM `".DB_PREFIX."_logistics_driver_detail` as ldd 
                    LEFT JOIN `".DB_PREFIX."_logistics_detail` as ld ON(ld.id = ldd.logistics_detail_id)
                    LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id)
                    WHERE sod.supply_o = '{$id}' AND ldd.driver = '{$current_driver}' AND ldd.status = '1'");
                    if($db->numRows($query) > 0){
                        $list = [];
                        while($r = $db->getRow($query)){
                            $list[] = $r["id"];
                        }
                        $list = implode(", ", $list);
                        $db->query("UPDATE `".DB_PREFIX."_logistics_driver_detail` SET driver = '{$driver}' WHERE id IN({$list})");
                    }
                }
            break;
        }
    break;
    case "delivery":
        switch(isset($url[4]) ? $url[4] : null){
            default:
                // logistics_delivery_detail();
                $LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
                $LogisticsDeliveryDetailTemp->view();
            break;
            case "detail_min":
                $LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
                $LogisticsDeliveryDetailTemp->view_clear();
            break;
            case "update_info":
                // echo logistics_delivery_detail_info();
                $LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
                echo $LogisticsDeliveryDetailTemp->detail_info();
            break;
            case "update_list":
                // echo logistics_delivery_detail_list();
                $LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
                echo $LogisticsDeliveryDetailTemp->detail_list();
            break;
            case "add":
                $result = add_logistics_delivery_route($_POST);
                \Logistics\Support\LogisticsDeliverySupport::notif();
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            case "is_create":
                echo \Logistics\Support\LogisticsDeliverySupport::is_create($_POST);
            break;
            case "cancel":
                \Logistics\Support\LogisticsDeliverySupport::cancel_delivery($_POST);
            break;
            case "print":
                \Logistics\LogisticsDeliveryDetailTemp::print($url[5], $_POST);
            break;
            case "x_update":
                $data = \Logistics\Support\LogisticsDeliverySupport::x_update($_POST);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
        }
    break;
    // case "delivery_archive2":
    //     $LogisticsDeliveryDetailArchiveTemp = new \Logistics\LogisticsDeliveryDetailArchiveTemp;
    //     $LogisticsDeliveryDetailArchiveTemp->view();
    // break;
    case "delivery_archive":
        switch(isset($url[4]) ? $url[4] : null){
            default:
                \Logistics\LogisticsDeliveryDetailArchiveTempClassic::grid();
            break;
            case "view":
                $LogisticsDeliveryDetailArchiveTempClassic = new \Logistics\LogisticsDeliveryDetailArchiveTempClassic;
                $LogisticsDeliveryDetailArchiveTempClassic->id = $url[5];
                $LogisticsDeliveryDetailArchiveTempClassic->view();
            break;
            case "fast-price-update":
               if($_POST['price'] && $_POST['id'])
               {   $new_price=$_POST['price'];
                   $id=$_POST['id'];
                   $db->query("UPDATE `".DB_PREFIX."_logistics_delivery` SET delivery_price = '{$new_price}' WHERE id = {$id}");
               }

            break;
        }
    break;
    case "logistics_delivery_driver":
        switch(isset($url[4]) ? $url[4] : null){
            case "add":
                logistics_delivery_driver_add($_POST);
            break;
            case "detail":
                $id = $url[5];
                $LogisticsDeliveryDriverDetailTemp = new \Logistics\LogisticsDeliveryDriverDetailTemp;
                $LogisticsDeliveryDriverDetailTemp->driver = $id;
                $LogisticsDeliveryDriverDetailTemp->view();
                // logistics_delivery_driver_detail($id);
            break;
            case "detail_min":
                $id = $url[5];
                $LogisticsDeliveryDriverDetailTemp = new \Logistics\LogisticsDeliveryDriverDetailTemp;
                $LogisticsDeliveryDriverDetailTemp->driver = $id;
                $LogisticsDeliveryDriverDetailTemp->view_clear();
            break;
            case "update_info":
                $id = $url[5];
                $LogisticsDeliveryDriverDetailTemp = new \Logistics\LogisticsDeliveryDriverDetailTemp;
                $LogisticsDeliveryDriverDetailTemp->driver = $id;
                echo $LogisticsDeliveryDriverDetailTemp->detail_info();
                // echo logistics_delivery_driver_detail_info($id);
            break;
            case "update_list":
                $id = $url[5];
                $LogisticsDeliveryDriverDetailTemp = new \Logistics\LogisticsDeliveryDriverDetailTemp;
                $LogisticsDeliveryDriverDetailTemp->driver = $id;
                echo $LogisticsDeliveryDriverDetailTemp->detail_list();
                // echo logistics_delivery_driver_detail_list($id);
            break;
            case "update_field":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : "";
                if($id && $field){
                    \Logistics\Model\LogisticsDelivery::findOrFail($id)->update([$field => $value]);
                }
            break;
            case "delivered":
                $id = $url[5];
                \Logistics\Support\LogisticsDeliverySupport::delivered($id);
            break;
            case "change_driver":
                $request = $_POST;
                $id = $request["id"] ?? false;
                $driver = $request["driver"] ?? false;
                if($id && $driver){
                    \Logistics\Model\LogisticsDelivery::findOrFail($id)->update(["driver" => $driver]);
                }
            break;
        }
    break;
    case "car_park":
        switch(isset($url[4]) ? $url[4] : null){
            default:
                CarParkTemplate::grid();
            break;
            case "create":
                CarParkTemplate::create();
            break;
            case "store":
                \Logistics\Model\CarPark::create($_POST);
                header("Location: /administration/module/logistics/car_park");
            break;
            case "destroy":
                \Logistics\Model\CarPark::destroy($url[5]);
            break;
        }
    break;
}
