<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

$dir = ROOT.'/usr/modules/boiler/admin/sub/';
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
    case "bid":
        switch(isset($url[4]) ? $url[4] : null) {
            default:
                bid_main();
            break;
            case "test":
                $id = 156;
                change_all_price_boiler_bid($id);
            break;
            case "add":
                $iid = bid_add();
                bid_notif_add($iid);
                header("Location: /administration/module/boiler/bid/detail/".$iid."");
            break;
            case "detail":
                $id = isset($url[5]) ? $url[5] : false;
                bid_detail($id);
            break;
            case "create_sales":
                $id = isset($url[5]) ? $url[5] : false;
                // $type = isset($_POST["type_id"]) ? $_POST["type_id"] : [];
                $request = $_POST;
                echo bid_create_sales($id, $request);
            break;
            case "addProduct":
                $id = isset($url[5]) ? $url[5] : false;
                addProduct($id, "https://atri-energo.ru/administration/module/boiler/bid/data_tovar_add/", "https://atri-energo.ru/administration/module/boiler/bid/data_detail_body/");
            break;
            case "addProductMin":
                $id = isset($url[5]) ? $url[5] : false;
                //addProduct($id, "https://atri-energo.ru/administration/module/boiler/bid/data_tovar_add/", "https://atri-energo.ru/administration/module/boiler/bid/data_detail_body/");
                addProduct($id, "", "", "#tov_list", "2", "boiler_bid");
            break;
            case "victoria_add":
                $id = isset($url[5]) ? $url[5] : false;
		        $victoria = new \Victoria;
		        $victoria->id = $id;
		        $victoria->url_add = "/administration/module/boiler/bid/victoria_tovar_add/{$id}";
		        $victoria->local_script = '$victoria.transfer_to_document_update = opener.update_body;';
		        echo $victoria->popup();
            break;
            case "victoria_min_add":
                $id = isset($url[5]) ? $url[5] : false;
		        $victoria = new \Victoria;
                $victoria->id = $id;
                $victoria->transfer_to_document_disabled = true;
                $victoria->local_script = <<<HTML
                    \$victoria.add_basket = async function(cid){
			        	const id = this.id;
                        opener.get_tov_item(id, cid).then(item => {
                            opener.replace_tov_item(id, item);
                            window.close();
                        });
			        }
HTML;
                echo $victoria->popup();
            break;
            case "get_tov_list":
                $id = isset($url[5]) ? $url[5] : false;
                get_tov_list($id);
            break;
            case "union_tov":
                $data = isset($_POST["union"]) ? $_POST["union"] : false;
                union_tov($data);
            break;
            case "append_user_chat":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $uid = isset($_POST["uid"]) ? $_POST["uid"] : false;
                append_user_chat($id, $uid);
            break;
            case "print":
                $id = isset($url[5]) ? $url[5] : false;
                bid_print($id);
            break;
            case "print_boiler_detail_sales":
                $id = isset($url[6]) ? $url[6] : false;
                $type = isset($url[5]) ? $url[5] : false;
                print_boiler_sales($id, $type, $_GET);
            break;
            case "send_notif_help":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $uid = isset($_POST["uid"]) ? $_POST["uid"] : false;
                $text = isset($_POST["text"]) ? $_POST["text"] : false;
                send_notif_help($id, $uid, $text);
            break;
            case "get_type_list":
                $id = isset($url[5]) ? $url[5] : false;
                get_type_list($id);
            break;

            case "get_type_list_spec":
                $id = isset($url[5]) ? $url[5] : false;
                get_type_list_spec($id);
            break;

            case "get_spec":
                get_spec();
            break;

            case "select_all_tov":
                $id = isset($url[5]) ? $url[5] : false;
                $checkbox = isset($_POST["checkbox"]) ? $_POST["checkbox"] : 1;
                select_all_tov($id, $checkbox);
            break;
            case "get_tov_item":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $cid = isset($_POST["cid"]) ? $_POST["cid"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                get_tov_item($id, $cid, $field);
            break;
            case "get_boiler_bid_data_detail_field":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                echo get_boiler_bid_data_detail_field($id, $field);
            break;
            case "get_all_file":
                $id = isset($url[5]) ? $url[5] : false;
                bid_get_all_file($id);
            break;
            case "get_client_info":
                $id = isset($url[5]) ? $url[5] : false;
                echo json_encode(get_client_info($id), JSON_UNESCAPED_UNICODE);
            break;
            case "get_all_total":
                $id = isset($url[5]) ? $url[5] : false;
                echo get_all_total($id);
            break;
            case "get_all_supply_total":
                $id = isset($url[5]) ? $url[5] : false;
                echo get_all_supply_total($id);
            break;
            case "data_detail":
                $id = isset($url[5]) ? $url[5] : false;
                bid_data_detail($id);
            break;
            case "data_detail_header":
                $id = isset($url[5]) ? $url[5] : false;
                bid_data_detail_header($id);
            break;
            case "data_detail_body":
                $id = isset($url[5]) ? $url[5] : false;
                bid_data_detail_body($id);
            break;
            case "save_data_detail":
                bid_save_data_detail();
            break;
            case "data_set_position":
                bid_data_set_position();
            break;
            case "del_tov_data_detail":
                $id = isset($url[5]) ? $url[5] : false;
                bid_del_tov_data_detail($id);
            break;
            case "data_tovar_add":
                $id = isset($url[5]) ? $url[5] : false;
                bid_data_tovar_add($id);
            break;
            case "victoria_tovar_add":
                $request = $_POST;
                $id = $request["id"];
                $items = $request['items'];
                $goods = [];
                foreach($items as $cid => $item){
                    $goods[] = [
                        "cid" => $cid,
                        "count" => $item["count"],
                    ];
                }
                bid_data_detail_add(["id" => $id, "goods" => $goods]);
            break;
            case "panel_progress":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_progress($id);
            break;
            case "panel_header":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_header($id);
            break;
            case "panel_main":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_main($id);
            break;
            case "panel_main_head":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_main_head($id);
            break;
            case "panel_main_body":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_main_body($id);
            break;
            case "panel_info":
                $id = isset($url[5]) ? $url[5] : false;
                get_panel_info($id);
            break;
            case "xeditable_change":
                $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
                $field = isset($_POST["name"]) ? $_POST["name"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : false;
                bid_save_field($id, $field, $value);
            break;
            case "xeditable_change_data":
                $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
                $field = isset($_POST["name"]) ? $_POST["name"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : false;
                bid_save_field_data($id, $field, $value);
            break;
            case "xeditable_change_type":
                $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
                $field = isset($_POST["name"]) ? $_POST["name"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : false;
                bid_save_field_type($id, $field, $value);
            break;
            case "xeditable_change_folder":
                $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
                $field = isset($_POST["name"]) ? $_POST["name"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : false;
                bid_save_field_folder($id, $field, $value);
            break;
            case "save_field":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_save_field($id, $field, $value);
            break;
            case "save_field_data":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_save_field_data($id, $field, $value);
            break;
            case "save_field_data_detail":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_save_field_data_detail($id, $field, $value);
            break;
            case "save_field_type":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_save_field_type($id, $field, $value);
            break;
            case "save_field_folder":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_save_field_folder($id, $field, $value);
            break;
            case "append_field_boiler_bid":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_append_field($id, $field, $value);
            break;
            case "remove_field_boiler_bid":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $field = isset($_POST["field"]) ? $_POST["field"] : false;
                $value = isset($_POST["value"]) ? $_POST["value"] : null;
                bid_remove_field($id, $field, $value);
            break;
            case "update_numb":
                $id = isset($url[5]) ? $url[5] : false;
                $org = isset($_POST["org"]) ? $_POST["org"] : 1;
                bid_update_numb($id, $org);
            break;
            case "save_files":
                $id = isset($url[5]) ? $url[5] : false;
                $file = isset($_POST["file"]) ? $_POST["file"] : false;
                bid_save_files($id, $file);
            break;
            case "del_files":
                $id = isset($url[5]) ? $url[5] : false;
                bid_del_files($id);
            break;
            case "change_allow_category":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                $status = isset($_POST["status"]) ? $_POST["status"] : false;
                $category = isset($_POST["category"]) ? $_POST["category"] : false;
                change_allow_category($id, $status, $category);
            break;
            case "del_boiler_bid_data":
                $id = isset($url[5]) ? $url[5] : false;
                del_boiler_bid_data($id);
            break;
            case "del_boiler_bid_folder":
                $id = isset($url[5]) ? $url[5] : false;
                del_boiler_bid_folder($id);
            break;
            case "add_boiler_bid_data":
                add_boiler_bid_data($_POST);
            break;
            case "section_select":
                $section = isset($_POST["section"]) ? $_POST["section"] : false;
                echo section_select($section);
            break;
            case "type_select":
                $section = isset($_POST["section"]) ? $_POST["section"] : false;
                $type = isset($_POST["type"]) ? $_POST["type"] : null;
                echo type_select($section, $type);
            break;
            case "get_boiler_bid":
                $data = isset($_POST["data"]) ? $_POST["data"] : [];
                $id = isset($_POST["id"]) ? $_POST["id"] : [];

                $result = get_boiler_bid($data, $id);
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            case "insert_filed_type":
                $data = isset($_POST["data"]) ? $_POST["data"] : [];
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                bid_insert_filed_type($id, $data);
            break;
            case "del_boiler_bid_type":
                $id = isset($_POST["id"]) ? $_POST["id"] : false;
                del_boiler_bid_type($id);
            break;
            case "notif_end_request":
                $id = isset($url[5]) ? $url[5] : false;
                if(check_end_status($id)){
                    send_notif_main_uid($id);
                }
            break;
            case "status_bid_auto":
                $id = isset($url[5]) ? $url[5] : false;
                set_status_bid($id);
            break;
            case "get_supply_price_temp":
                $id = isset($url[5]) ? $url[5] : false;
                $detail = get_supply_price_m($id);
                get_supply_price_temp($id, $detail);
            break;
            case "get_folder":
                $id = isset($url[5]) ? $url[5] : false;
                echo json_encode(get_folder($id), JSON_UNESCAPED_UNICODE);
            break;
            case "folder_doit":
                $id = isset($url[5]) ? $url[5] : false;
                $data = $_POST;
                folder_doit($id, $data);
            break;
            case "add_boiler_bid_users":
                $data = $_POST;
                boiler_bid_users_add($data);
            break;
            case "del_boiler_bid_users":
                $id = isset($url[5]) ? $url[5] : false;
                boiler_bid_users_del($id);
            break;
            case "get_weight":
                $id = isset($url[5]) ? $url[5] : false;
                $weight = get_weight_boiler_bid($id);
                echo json_encode($weight, JSON_UNESCAPED_UNICODE);
            break;
            case "load_file":
                $id = isset($url[5]) ? $url[5] : false;
                $ftp = new \StorageFTP;
                $ftp->route_file_id($id);
            break;
            case "extendedSort":
                $id = isset($url[5]) ? $url[5] : false;
                if($id){
                    $request = $_POST;
                    $request["data"] = bid_data_detail_body_data($id);
                    $extendedSort = new \ExtendedSort;
                    $data = $extendedSort->sort($request);
                    $position = 1;
                    foreach($data as $item){
                        bid_save_field_data_detail($item["id"], "position", $position);
                        $position++;
                    }
                }
            break;
            case "bid_files":
                bid_files($_POST);
            break;
            case "delete_sales_link";
                $request = $_POST;
                $bid_id = $request["bid_id"];
                $sales_id = $request["sales_id"];
                bid_delete_sales_link($bid_id, $sales_id);
            break;
            // case "sort_fix":
            //     $query = $db->query("SELECT * FROM JMY_boiler_bid");
            //     while($r = $db->getRow($query)){
            //         $q = $db->query("SELECT * FROM JMY_boiler_bid_type WHERE bid_id = '".$r["id"]."'");
            //         $i = 0;
            //         if($db->numRows($q) > 0){
            //             while($p = $db->getRow($q)){
            //                 $db->query("UPDATE JMY_boiler_bid_type SET position = '".$i."' WHERE id = '".$p["id"]."' LIMIT 1");
            //                 $i++;
            //             }
            //         }
            //     }
            // break;
        }
    break;
}
