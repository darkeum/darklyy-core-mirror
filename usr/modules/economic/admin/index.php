<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/economic/admin/sub/';  
includeSub($dir);

use \Economic\BalanceController;

switch(isset($url[3]) ? $url[3] : null) {
	default:
        $adminTpl->admin_head();
        echo "Economic";
        $adminTpl->admin_foot();
	break;
    case "act":
        switch(isset($url[4]) ? $url[4] : null) {
            default:
                // $ActListController = new \Economic\ActListController;
                // $ActListController->view();
                main();
            break;
            case "editor":
                $ActEditorController = new \Economic\ActEditorController;
                $ActEditorController->id = $url[5] ?? false;
                if($ActEditorController->id){
                    $ActEditorController->init_act();
                }
                $ActEditorController->view();
            break;
            case "editor_tab_body":
                $ActEditorController = new \Economic\ActEditorController;
                echo json_encode($ActEditorController->tab_body_init($_POST), JSON_UNESCAPED_UNICODE);
            break;
            case "store":
                $request = $_POST;
                $request["uid_created"] = $core->auth->user_info['id'];
                $request = json_request($request);
                $ActReconciliation = \Economic\Model\ActReconciliation::create($request);
                echo $ActReconciliation->toJson();
            break;
            case "update":
                $request = $_POST;
                $request = json_request($request);
                $ActReconciliation = \Economic\Model\ActReconciliation::findOrFail($url[5]);
                $ActReconciliation->update($request);
                echo $ActReconciliation->toJson();
            break;
            case "view":
                $ActController = new \Economic\ActViewController;
                $ActController->id = $url[5];
                $ActController->init_act();
                $ActController->view();
            break;
            case "print":
                $ActController = new \Economic\ActPrintController;
                $ActController->id = $url[5];
                $ActController->init_act();
                $ActController->print();
            break;
        }
    break;
    case "balance":
        $BalanceController = new BalanceController;
        switch(isset($url[4]) ? $url[4] : null) {
            default:
                $BalanceController->grid();
            break;
            case "editor":
                $BalanceController->oid = $url[5];
                $BalanceController->editor();
            break;
            case "create":
                $BalanceController->create($_POST);
            break;
            case "delete":
                $BalanceController->delete($url[5]);
            break;
            case "view_list":
                $BalanceController->oid = $url[5];
                echo $BalanceController->view_list();
            break;
            case "reestr_options":
                echo BalanceController::reestr_opt($url[5], $url[6]);
            break;
        }
    break;
}

function json_request($request){
    if(isset($request["records"]))                      $request["records"] =                       json_decode($request["records"], true);
    if(isset($request["org_data"]))                     $request["org_data"] =                      json_decode($request["org_data"], true);
    if(isset($request["org_director"]))                 $request["org_director"] =                  json_decode($request["org_director"], true);
    if(isset($request["oid_data"]))                     $request["oid_data"] =                      json_decode($request["oid_data"], true);
    if(isset($request["initial_balance_contract"]))     $request["initial_balance_contract"] =      json_decode($request["initial_balance_contract"], true);
    return $request;
}
