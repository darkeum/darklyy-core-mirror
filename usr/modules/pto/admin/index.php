<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/pto/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
    default:
        $tpto = new \PTO\TPTO;
        $tpto->main();
    break;
    case "add":
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $pto = \Pto\Model\Pto::create($_POST);
            echo json_encode(["id" => $pto->id], JSON_UNESCAPED_UNICODE);
        } else {
            $tpto = new \PTO\TPTO;
            $tpto->template();
        }
    break;
    case "edit":
        $id = $url[4];
        $tpto = new \PTO\TPTO;
        $tpto->template(["id" => $id]);
    break;
    case "update":
        $id = $url[4];
        \Pto\Model\Pto::findOrFail($id)->update($_POST);
    break;
    case "load_file":
        $data = pto_load_file($_FILES);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        // echo json_encode(["attach_id" => 123], JSON_UNESCAPED_UNICODE);
    break;
    case "load_ftp":
        $id = $url[4];
        $ftp = new \StorageFTP;
        $ftp->get_file_id($id);
    break;
    case "view":
        $tpto = new \PTO\TPTOView;
        $pto_id = $url[4];
        switch(isset($url[5]) ? $url[5] : null) {
            default:
                $tpto->main($pto_id);
            break;
            case "add":
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $ptoview = \Pto\Model\PtoView::create($_POST);
                    echo json_encode(["id" => $ptoview->id], JSON_UNESCAPED_UNICODE);
                } else {
                    $tpto->template(["pto_id" => $pto_id]);
                }
            break;
            case "edit":
                $id = $url[6];
                $tpto->template(["pto_id" => $pto_id, "id" => $id]);
            break;
            case "update":
                $id = $url[6];
                \Pto\Model\PtoView::findOrFail($id)->update($_POST);
            break;
        }
    break;
    case "price":
        $pto_id = $url[4];
        $tpto = new \PTO\TPTOPrice;
        switch(isset($url[5]) ? $url[5] : null) {
            default:
                $tpto->main($pto_id);
            break;
            case "add":
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $ptoprice = \Pto\Model\PtoPrice::create($_POST);
                    echo json_encode(["id" => $ptoprice->id], JSON_UNESCAPED_UNICODE);
                } else {
                    $tpto->template(["pto_id" => $pto_id]);
                }
            break;
            case "edit":
                $id = $url[6];
                $tpto->template(["pto_id" => $pto_id, "id" => $id]);
            break;
            case "update":
                $id = $url[6];
                \Pto\Model\PtoPrice::findOrFail($id)->update($_POST);
            break;
        }
    break;
}
