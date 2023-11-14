<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/merging/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
        main();
    break;
    case "add":
        add();
    break;
    case "save":
        $data = $_POST;
        save($data);
    break;
    case "detail":
        $id = isset($url[4]) ? $url[4] : false;
        detail($id);
    break;
    case "detail_header_title":
        $id = isset($url[4]) ? $url[4] : false;
        echo detail_header_title($id);
    break;
    case "detail_header":
        $id = isset($url[4]) ? $url[4] : false;
        detail_header($id);
    break;
    case "detail_body":
        $id = isset($url[4]) ? $url[4] : false;
        detail_body($id);
    break;
    case "detail_body_btn":
        $id = isset($url[4]) ? $url[4] : false;
        detail_body_btn($id);
    break;
    case "addProduct":
        $id = isset($url[4]) ? $url[4] : false;
        addProduct($id, "https://atri-energo.ru/administration/module/merging/tovar_add/", "https://atri-energo.ru/administration/module/merging/detail_body/", "#tov_list", 1, 'merging');
    break;
    case "addProduct2":
        $id = isset($url[4]) ? $url[4] : false;
        addProduct($id, "https://atri-energo.ru/administration/module/merging/tovar_add_main/", "https://atri-energo.ru/administration/module/merging/detail_body/", "#tov_list", 2, 'merging');
    break;
    case "victoria_add":
        $id = isset($url[4]) ? $url[4] : false;
        $victoria = new \Victoria;
		$victoria->id = $id;
        $victoria->url_add = "/administration/module/merging/tovar_add";
        $victoria->local_script = '$victoria.transfer_to_document_update = opener.updateBody;';
        echo $victoria->popup();
    break;
    case "victoria_add2":
        $id = isset($url[4]) ? $url[4] : false;
        $victoria = new \Victoria;
        $victoria->id = $id;
        $victoria->transfer_to_document_disabled = true;
        $victoria->local_script = <<<HTML
			\$victoria.add_basket = async function(cid){
				const id = this.id;
                $.post('/administration/module/merging/tovar_add_main', {cid, id}, function(){
                    opener.updateHeader();
                    opener.updateBody();
                    window.close();
                });
			}
HTML;
        echo $victoria->popup();
    break;
    case "tovar_add":
        $data = $_POST;
        tovar_add($data);
    break;
    case "tovar_add_main":
        $data = $_POST;
        tovar_add_main($data);
    break;
    case "x_change_filed":
        $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
        $field = isset($_POST["name"]) ? $_POST["name"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        update_filed($id, $field, $value);
    break;
    case "x_change_filed_detail":
        $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
        $field = isset($_POST["name"]) ? $_POST["name"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        update_filed_detail($id, $field, $value);
    break;
    case "remove_item":
        $id = isset($url[4]) ? $url[4] : false;
        remove_item($id);
    break;
    case "check_minus_count_merging":
        $id = isset($url[4]) ? $url[4] : false;
        check_minus_count_merging($id);
    break;
    case "check_minus_count_merging2":
        $id = isset($url[4]) ? $url[4] : false;
        check_minus_count_merging2($id);
    break;
    case "get_merging_data":
        $id = isset($url[4]) ? $url[4] : false;
        echo json_encode(query_main_result($id), JSON_UNESCAPED_UNICODE);
    break;
    case "end_quest":
        $id = isset($url[4]) ? $url[4] : false;
        end_quest($id);
    break;
    case "start_quest":
        $id = isset($url[4]) ? $url[4] : false;
        start_quest($id);
    break;
    case "check_cid_have":
        $id = isset($url[4]) ? $url[4] : false;
        echo check_cid_have($id);
    break;
    case "update_filed":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $field = isset($_POST["field"]) ? $_POST["field"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        update_filed($id, $field, $value);
    break;
    case "update_filed_detail":
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $field = isset($_POST["field"]) ? $_POST["field"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        update_filed_detail($id, $field, $value);
    break;
    case "print":
        $id = isset($url[4]) ? $url[4] : false;
        merging_print($id);
    break;
    case "create_transfer_merging":
        echo create_transfer_merging($_POST);
    break;
    case "create_merging_additional":
        $id = isset($url[4]) ? $url[4] : false;
        $merging_additional = \Merging\Model\MergingAdditional::create(["merging_id" => $id]);
        echo $merging_additional->toJson();
    break;
    case "additional":
        \Merging\MergingAdditionalTemp::index();
    break;
    case "detail_additional":
        $id = isset($url[4]) ? $url[4] : false;
        \Merging\MergingAdditionalTemp::show($id, $_GET);
    break;
    case "victoria_add_additional":
        $id = isset($url[4]) ? $url[4] : false;
        $victoria = new \Victoria;
		$victoria->id = $id;
        $victoria->url_add = "/administration/module/merging/tovar_add_additional";
        $victoria->local_script = '$victoria.transfer_to_document_update = opener.updateBody;';
        echo $victoria->popup();
    break;
    case "tovar_add_additional":
        $request = $_POST;
        $id = isset($request["id"]) ? $request["id"] : false;
	    $items = isset($request["items"]) ? $request["items"] : [];
	    if($id && count($items) > 0){
	    	foreach($items as $cid => $item){
                $catalog = \Catalog_v2\Model\Catalog::findOrFail($cid);
                \Merging\Model\MergingDetailAdditional::create([
                    "merging_additional_id" => $id,
                    "cid" => $cid, "name" => $catalog->title, "unit" => $catalog->unit, "sku" => $catalog->sku, "count" => $item["count"]
                ]);
	    	}
	    }
    break;
    case "x_change_additional":
        $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
        $field = isset($_POST["name"]) ? $_POST["name"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        $merging = \Merging\Model\MergingAdditional::findOrFail($id);
        $merging->{$field} = $value;
        $merging->save();
    break;
    case "x_change_detail_additional":
        $id = isset($_POST["pk"]) ? $_POST["pk"] : false;
        $field = isset($_POST["name"]) ? $_POST["name"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : false;
        $merging = \Merging\Model\MergingDetailAdditional::findOrFail($id);
        $merging->{$field} = $value;
        $merging->save();
    break;
    case "destroy_additional":
        $id = isset($url[4]) ? $url[4] : false;
        \Merging\Model\MergingDetailAdditional::destroy($id);
    break;
    case "additional_check_minus_count":
        $id = isset($url[4]) ? $url[4] : false;
        $check = \Merging\MergingAdditionalTemp::check_minus_count($id);
        echo json_encode($check, JSON_UNESCAPED_UNICODE);
    break;
    case "additional_end":
        $id = isset($url[4]) ? $url[4] : false;
        $data = \Merging\MergingAdditionalTemp::end($id, $_POST);
        echo $data->toJson();
    break;
    case "additional_print":
        $id = isset($url[4]) ? $url[4] : false;
        \Merging\MergingAdditionalTemp::print($id);
    break;
}
