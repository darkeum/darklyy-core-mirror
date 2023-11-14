<?php

use Boot\Support\Currency;
	
	/**
		* @name        JMY CORE
		* @link        https://jmy.su/
		* @copyright   Copyright (C) 2012-2019 JMY LTD
		* @license     LICENSE.txt (see attached file)
		* @version     VERSION.txt (see attached file)
		* @author      Komarov Ivan
		* @info_ru     Место под ваши собственные функции для ajax
		* @info_en     Place for your own functions for ajax
    */
	
	global $core, $db, $cats, $allowFuncs;
	
	$allowFuncs = array_merge($allowFuncs, ["getSearchList", "getIdProduct", "getCat", "getCert", "getImgSelect", "getAttr", "getBoiler", "getCategory", "addGoods", "getUnit", "checkSku", "fastBarUniversalSave", "getFilter", "getPriceType", "getOrganizations", "getOrgToSelect", "getObjectToSelect", "getUsersToSelect", "getSalesToSelect", "getRealizationToSelect", "getSupplyOrderToSelect", "getCatalogManufactureToSelect", "getMergingToSelect", "getCatalogToSelect", "getCatalogGroupsToSelect", "add_message_chat", "load_chat_list", "load_chat_message", "load_users_list", "chat_save_file", "change_uid_chat", "find_chat", "find_chat_all", "find_and_load_chat_lists", "check_chat_site", "get_chat_data", "getUsersAllToSelect", "load_list_union", "relationsAddAjax", "relationsDeleteAjax", "relationsFindAjax", "get_org_data_ajax", "read_chat_message", "notif_send_message", "change_chat", "get_all_no_read_message_count", 'setPushToken', 'clipboard', 'clipboard_data', "selected_find", "add_chat", "get_ftp_file", "request_god", "victoria_load_list", "victoria_get_item", "victoria_filter", "victoria_cert", "victoria_attribute", "extendedSortGetCategory", "extendedSortTemplateSave", "extendedSortTemplateGet", "extendedSortTemplateDelete", "getPriceTypeSelectData"]);
	
	function getSearchList(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["data"], $_POST["check_full"], $_POST["loaded_max"], $_POST["check"], $_POST["searchCat"], $_POST["filterData"], $_POST["orderField"], $_POST["orderType"], $_POST["price_type"])){
			$search = trim($_POST["data"]);
			$search = htmlspecialchars($search, ENT_QUOTES);
			$search = str_replace("\\", "", $search);
            $loaded_max = $_POST["loaded_max"];
            $check = $_POST["check"];
			$check_full = $_POST["check_full"];
			$check_expert = $_POST["check_expert"];
			$check_active = isset($_POST["check_active"]) ? $_POST["check_active"] : 'true';
            $searchCat = $_POST["searchCat"];
            $filterData = $_POST["filterData"];
            $filterDataMan = isset($_POST["filterDataMan"]) ? json_decode($_POST["filterDataMan"], true) : [];
            $orderField = $_POST["orderField"];
            $orderType = $_POST["orderType"];
            $price_type = $_POST["price_type"];
            $stock_id = isset($_POST["stock_id"]) ? $_POST["stock_id"] : 1;
            
            if($check == "true") {
				//$checkWhere = "(c.title LIKE '".$search."' OR c.sku LIKE '".$search."' OR c.id LIKE '".$search."')";
				$checkWhere = "(c.title LIKE '".$search."' OR c.sku LIKE '".$search."' OR c.sku2 LIKE '%".$search."%' OR c.sku3 LIKE '%".$search."%' OR c.id LIKE '".$search."' OR cm.name LIKE '%".$search."%')";
			} else {
				$searchMas = explode(" ", $search);
				$checkWhere = "(";
				foreach($searchMas as $val){
					//$checkWhere .= "(c.title LIKE '%".$val."%' OR c.sku LIKE '%".$val."%' OR c.id LIKE '".$search."') AND ";
					$checkWhere .= "(c.title LIKE '%".$val."%' OR c.sku LIKE '%".$val."%' OR c.sku2 LIKE '%".$val."%' OR c.sku3 LIKE '%".$val."%' OR c.id LIKE '".$search."' OR cm.name LIKE '%".$search."%') AND ";
            	}
				
				$checkWhere = mb_substr($checkWhere, 0, -4);
				$checkWhere .= ")";
            }
			if($check_full == "true") {
				 if($check == "true") {
					if ($check_expert == "true")
					{
						$searchStr = $search;
					}
					else
					{
						$searchStr = str_replace("-", " ", $search);
						$searchStr = str_replace("+", " ", $searchStr);
						$searchStr = str_replace("*", " ", $searchStr);
						$searchStr = str_replace('"', " ", $searchStr);
						$searchStr = str_replace('~', " ", $searchStr);
						$searchStr = str_replace('(', " ", $searchStr);
						$searchStr = str_replace(')', " ", $searchStr);
						$searchStr = str_replace('<', " ", $searchStr);
						$searchStr = str_replace('>', " ", $searchStr);
					}
					$searchMas = explode(" ", $search);
					$searchStr = '';
					foreach($searchMas as $val){
						$searchStr .= '+'.$val.' ';
					}
				 }
				 else				 
				 {
					if ($check_expert == "true")
					{
						$searchStr = $search;
					}
					else
					{
						$searchStr = str_replace("-", " ", $search);
						$searchStr = str_replace("+", " ", $searchStr);
						$searchStr = str_replace("*", " ", $searchStr);
						$searchStr = str_replace('"', " ", $searchStr);
						$searchStr = str_replace('~', " ", $searchStr);
						$searchStr = str_replace('(', " ", $searchStr);
						$searchStr = str_replace(')', " ", $searchStr);
						$searchStr = str_replace('<', " ", $searchStr);
						$searchStr = str_replace('>', " ", $searchStr);
					}
				 }
				 if (!empty($search))
				 {
					//$checkWhere = "MATCH (c.title) AGAINST ('".$searchStr."' IN BOOLEAN MODE)  OR c.sku LIKE '".$search."' OR c.id LIKE '".$search."' ";
					$checkWhere = "MATCH (c.title) AGAINST ('".$searchStr."' IN BOOLEAN MODE)  OR c.sku LIKE '".$search."' OR c.sku2 LIKE '".$search."' OR c.sku3 LIKE '".$search."' OR c.id LIKE '".$search."' ";
				 }
				 else
				{
					$checkWhere = 'title IS NOT NULL';
				 }
			}
			
            $searchCatWhere = "";
            if($searchCat != "false"){
				$categories = get_all_categories_catalog();
				$categories_list = get_children_category($searchCat, $categories);
				$searchCatWhereArr = [];
				foreach($categories_list as $cl){
					$searchCatWhereArr[] = "c.category = ',".$cl.",'";
				}
				$searchCatWhere = " AND (".implode(" OR ", $searchCatWhereArr).")";
				// $searchCatWhere = " AND (c.category = ',".$searchCat.",')";
			} 
			/*
			$filterDataWhere = "";
			if(mb_strlen($filterData) > 0){
				$filterDataJson = json_decode($filterData, true);
				if(!empty($filterDataJson)){
					$filterDataWhere = " AND (";
					foreach($filterDataJson as $key => $val){
						$str = '"'.$filterDataJson[$key][0].'":"'.$filterDataJson[$key][1].'"';
						$filterDataWhere .= "c.attribute LIKE '%".$str."%' OR ";
					}
					$filterDataWhere = mb_substr($filterDataWhere, 0, -4);
					$filterDataWhere .= ")";
				}
			}
			*/
			$filterDataWhere = "";
			if(mb_strlen($filterData) > 0){
				$filterDataJson = json_decode($filterData, true);
				foreach($filterDataJson as $key => $val){
					if(count($val) > 0){
						$filterDataWhere .= " AND (";
						foreach($val as $k => $v){
							$str = '"'.$filterDataJson[$key][$k][0].'":"'.$filterDataJson[$key][$k][1].'"';
							$str1 = '"'.$filterDataJson[$key][$k][0].'":'.$filterDataJson[$key][$k][1].'';
							$filterDataWhere .= "c.attribute LIKE '%".$str."%' OR c.attribute LIKE '%".$str1."%' OR ";
						}
						$filterDataWhere = mb_substr($filterDataWhere, 0, -4);
						$filterDataWhere .= ")";
					}
				}
			}

			$filterDataManWhere = "";
			if(count($filterDataMan) > 0){
				$filterDataMan = implode(", ", $filterDataMan);
				$filterDataManWhere .= " AND (cm.id IN(".$filterDataMan."))";
			}
			$page = 25;

			$checkActive = "";
			if($check_active === 'true'){
				$checkActive = " AND c.active = '1'";
			}

			/*
			$strQuery = "SELECT c.id, c.title, c.sku, c.quantity, c.attribute, c.unit, c.man, cm.name as cmname FROM `" . DB_PREFIX . "_catalog` as c LEFT JOIN ".DB_PREFIX."_catalog_manufacture as cm ON(cm.id = c.man) WHERE ".$checkWhere.$searchCatWhere.$filterDataWhere.$filterDataManWhere." AND c.hidden = '0' ORDER BY ".$orderField." ".$orderType." LIMIT ".$loaded_max*$page.", ".$page."";
			*/

			//LIMIT 18446744073709551615
			$strQuery = "SELECT c.id, c.title, c.sku, sd.sum as quantity, c.attribute, c.unit, cu.name as cuname, c.man, cm.name as cmname, cpd.currency, cpd.sum as sum, cpd.price_type, c.category
			FROM `" . DB_PREFIX . "_catalog` as c 
			LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as cm ON(cm.id = c.man)
			LEFT JOIN (
                SELECT cid, sum, stock FROM `".DB_PREFIX."_stock_detail` WHERE stock = {$stock_id}
            ) as sd ON(sd.cid = c.id)
			LEFT JOIN `".DB_PREFIX."_catalog_unit` as cu ON(cu.unit = c.unit) 
			LEFT JOIN (
				SELECT cpd.cid, cpd.sum, cp.price_type, cpt.currency
				FROM JMY_catalog_price_detail as cpd 
				LEFT JOIN JMY_catalog_price as cp ON(cp.id = cpd.price_id)
				LEFT JOIN JMY_catalog_price_type as cpt ON(cpt.id = cp.price_type)
                WHERE cp.active = '1' AND cp.price_type = '".$price_type."' HAVING 1 ORDER BY cp.date
			) as cpd ON(cpd.cid = c.id)
			WHERE ".$checkWhere.$searchCatWhere.$filterDataWhere.$filterDataManWhere.$checkActive." AND c.hidden = '0' 
			GROUP BY c.id
			ORDER BY ".$orderField." ".$orderType." 
			LIMIT ".$loaded_max*$page.", ".$page."";

			//echo $strQuery;

			$query = $db->query($strQuery);
            
            while($r = $db->getRow($query)){
				$cert = $db->query("SELECT COUNT(*) as count FROM JMY_cert_relation WHERE cid = ".$r["id"]);
				$cert_count = $db->getRow($cert);

				/*
				$qp = $db->query("SELECT cpd.sum FROM JMY_catalog_price_detail as cpd LEFT JOIN JMY_catalog_price as cp ON(cp.id = cpd.price_id) WHERE cpd.cid = '".$r["id"]."' AND cp.status = 1 AND cp.active = 1 AND cp.price_type = '".$price_type."' ORDER BY cp.date DESC LIMIT 1");
				$price = $db->getRow($qp);
				*/

				//$l_price = $price["sum"];
				// $l_price = $r["sum"];

				// $sum = $l_price;

				if($r["currency"] == 643){
					$sum = $r["sum"];
				} else {
					if(empty($r["sum"])){
						$sum = 0;
					} else {
						$sum = get_manufacture_price([
							"cid" => $r["id"],
							"manufacture_price" => round($r["sum"] * Currency::get_exchange()->get($r["currency"]), 2),
							"price_type" => $r["price_type"],
							"manufacture_id" => $r["man"],
						]);
					}
				}

			

				if(empty($sum)){
					$sum = 'Отсутствует';
				} else {
					$sum = number_format($sum,2,'.',' ')." руб.";
				}

				//$ndr = getNDR($r["id"], '1', 'sales');
				$ndr = get_ndr(["cid" => $r["id"], "stock_id" => $stock_id, "module" => "sales", "pid" => null, "returnType" => "view"]);

				$title = html_entity_decode(htmlspecialchars_decode($r["title"], ENT_QUOTES), ENT_QUOTES);

				//get_unit('data', $r["unit"],'name')

				$category_btn_dis = empty($r["category"]) ? "disabled" : "";

                echo '<tr id=res_'.$r["id"].' class="catalog">
				<td style="vertical-align: middle;" class="title" title="'.$r["id"].'">'.$title.'</td>
				<td style="vertical-align: middle;" align="center">'.$r["sku"].'</td>
				<td style="vertical-align: middle;" align="center">'.$r["cmname"].'</td>
				<td style="vertical-align: middle;" align="center">'.$sum.'</td>
				<td style="vertical-align: middle;" align="center" class="ndr" onclick="view_ndr_modal('.$r["id"].');">'.$ndr.'</td>
				<td style="vertical-align: middle;" align="center">'.$r["cuname"].'</td>				
				<td style="vertical-align: middle; width:150px;" align="center">
				<button class="btn btn-success addBas" onclick="AddBas(this);" id="btn_'.$r["id"].'" '.$category_btn_dis.'><i class="fa fa-plus" aria-hidden="true"></i></button>&nbsp;
				<button '.(($cert_count['count'] > 0) ? 'class="btn btn-primary spec"' : 'class="btn btn-info spec" disabled').'><i class="fas fa-file-alt" aria-hidden="true"></i></button>&nbsp;
				<button '.(empty($r["attribute"]) ? 'dd' : '').' class="btn btn-warning attr"><i class="fas fa-cog"></i></button>
				</td>
                </tr>';
            }
            
        }
    }
	

	
	function getIdProduct(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["data"], $_POST["price_type"])){
            $id = $_POST["data"];
            $price_type = $_POST["price_type"];
            $query = $db->query("SELECT c.*, cu.name as cuname, cm.name as cmname FROM `" . DB_PREFIX . "_catalog` as c LEFT JOIN `" . DB_PREFIX . "_catalog_unit` as cu ON(c.unit = cu.unit) LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as cm ON(c.man = cm.id) WHERE c.id = '".$id."' LIMIT 1");
			$r = $db->getRow($query);
			
			$qp = $db->query("SELECT cpd.sum FROM JMY_catalog_price_detail as cpd LEFT JOIN JMY_catalog_price as cp ON(cp.id = cpd.price_id) WHERE cpd.cid = '".$r["id"]."' AND cp.status = 1 AND cp.active = 1 AND cp.price_type = '".$price_type."' ORDER BY cp.date DESC LIMIT 1");
			$price = $db->getRow($qp);

			$sum = empty($price["sum"]) ? 0 : $price["sum"];
			if($price_type == 2){
				$sum = round($sum * Currency::get_exchange()->get(978), 2);
			}
			$sum_only = $sum;
			if(empty($sum)){
				$sum = 'Отсутствует';
			} else {
				$sum .= ' руб.';
			}

			$ndr_view = get_ndr(["cid" => $r["id"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

            echo '<tr id=bas_'.$id.' class="basket">
			<td style="vertical-align: middle;">'.$r["title"].'</td>
			<td style="vertical-align: middle;" align="center">'.$r["sku"].'</td>
			<td style="vertical-align: middle;" align="center">'.$r["cmname"].'</td>
			<td id="price_'.$id.'" data-price="'.$sum_only.'" style="vertical-align: middle;" align="center">'.$sum.'</td>
			<td style="vertical-align: middle;" align="center"><input onchange="getTotal('.$id.');" type="number" value="0" id="discount_'.$id.'" name="discount_'.$id.'" class="discount"  min="-100" max="1000"  style="width: 90%;"></td>
			<td style="vertical-align: middle;" align="center">'.$ndr_view.'</td>
			<td style="vertical-align: middle;" align="center">'.$r["cuname"].'</td>			
			<td style="vertical-align: middle;" align="center"><input onchange="getTotal('.$id.');" type="number" value="1" id="count_'.$id.'" name="count_'.$id.'" class="count" min="1" max="100000" style="width: 100%;"></td>			
			<td style="vertical-align: middle;" align="center"><input onchange="getDiscount('.$id.');" type="number" value="'.$sum_only.'" id="total_'.$id.'" name="total_'.$id.'" class="total"></td>
			<td style="vertical-align: middle;" align="center"><a href="javascript:void(0);" id="rem_'.$id.'" class="btn btn-danger remove" tabindex="-1"><i class="fa fa-minus" aria-hidden="true"></i></a></td>
			</tr>
			<input type="hidden" value="'.$id.'" id="bas2_'.$id.'" class="bas2" name="cid[]">';
        }
    }	
	
	function getCat(){
		global $core, $db, $cats, $allowFuncs;
		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_categories` WHERE module = 'catalog_v2' ORDER BY name");
        if($db->numRows($query) > 0){
            $cats = [];
            while($cat = $db->getRow($query)){
                $cats[$cat['parent_id']][$cat['id']] = $cat;
			}

            echo tree_build($cats,0,true);
        }
    }		
	
	function getCert(){
		$victoria = new \Victoria;
		$data = $victoria->cert($_POST);
		echo $data["template"];
    }
	
	function getImgSelect(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["id"])){
			$id = $_POST["id"];
			$img = $db->query("SELECT preview FROM JMY_catalog WHERE id = ".$id);
			$r = $db->getRow($img);
			if(!empty($r["preview"])){
				echo '<img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="'.$r["preview"].'">';
				} else {
				echo '<img style="display: block; margin-left: auto; margin-right: auto; width: 50%;" src="/usr/other/sub/selection/img/no_img.png">';
            }
        }
    }
	
	function getAttr(){
		$victoria = new \Victoria;
		$data = $victoria->attribute($_POST);
		echo $data["template"];
	}	
	
	function getCategory(){
		global $core, $db, $cats, $allowFuncs;
		$cats_arr = $core->aCatList('catalog_v2');	
		echo '<option value="0" selected>Отсутсвует</option>';
		foreach($cats_arr as $cid => $name){
			echo '<option value="'.$cid.'">'.$name.'</option>';
        }
	}
	
	function getPriceType(){
		global $core, $db, $cats, $allowFuncs;
			$query = $db->query("SELECT id, title FROM JMY_catalog_price_type");	
			while($r = $db->getRow($query)){
				$selected = "";
				if($r["id"] == "3"){
					$selected = 'selected';
				}
				echo '<option value="'.$r["id"].'" '.$selected.'>'.$r["title"].'</option>';
			}
    }
	
	function addGoods(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["title"], $_POST["sku"], $_POST["unit"], $_POST["category"], $_POST["types"], $_POST["man"])){
			$catalog = Catalog_v2\Model\Catalog::create($_POST);
			// $request = [];
			// $request["title"] = $_POST['title'];
			// $request["sku"] = $_POST['sku'];
			// $request["unit"] = $_POST['unit'];
			// $request["category"] = $_POST['category'];
			// $request["types"] = $_POST['types'];
			// $request["man"] = $_POST['man'];
			// $id = $catalog->add();
			echo json_encode(["id" => $catalog->id], JSON_UNESCAPED_UNICODE);
        }
    }
	
	function getUnit(){
		global $core, $db, $cats, $allowFuncs;
		$unit = $db->query("SELECT * FROM JMY_catalog_unit");
		$i=0;
		while($r = $db->getRow($unit)){
			if(empty($r["name"])) continue;
			echo '<option value="'.$r["unit"].'" '.($i == 0 ? "selected" : "").'>'.$r["name"].'</option>';
			$i++;
        }
    }
	
	function checkSku(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["sku"], $_POST["cat"])){
			$sku = $_POST["sku"];
			$cat = $_POST["cat"];
			if($sku == "AE05".$cat."XXXXXX"){
				echo "true";
        	} else {
				$query = $db->query("SELECT sku FROM JMY_catalog WHERE sku LIKE '".$sku."'");
				if($db->numRows($query) > 0){
					echo "false";
					} else {
					echo "true";
				}
			}
		}
	}

	function getFilter()
	{
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["cat"], $_POST["search"]))
		{
			$cat = $_POST["cat"];			
			$search = $_POST["search"];	
			//$search = '';	
			$whereSearch = '';	
			if(!empty($search)){
				$whereSearch = " AND (title LIKE '%".$whereSearch."%' OR sku LIKE '%".$whereSearch."%')";
			}	
			$whereSearch .= " AND hidden = 0";
			$siteWhere = "";
			if(isset($_POST["site"])){
				$siteWhere = " AND hidden = 0 AND active = 1";
			}
			$atribute_query = $db->query("SELECT * FROM JMY_catalog_atribute WHERE cat LIKE '%,".$cat.",%' ");
			$atribute_value_query = $db->query("SELECT attribute, man FROM JMY_catalog WHERE category LIKE ',".$cat.",'".$whereSearch.$siteWhere."");
			$ajax_filter = array();
			$array_man = [];

			
			

			//проход по категориям 
			if($db->numRows($atribute_query) > 0)
			{
				while($atribute_list = $db->getRow($atribute_query)) 
				{
					$ajax_filter[$atribute_list['id']]['title'] = $atribute_list['title'];
					$ajax_filter[$atribute_list['id']]['value'] = array();	
					$ajax_filter[$atribute_list['id']]['value_is'] = array();	
				}
				//проход по товарам
				if($db->numRows($atribute_value_query) > 0)
				{
					while($atribute_value_list = $db->getRow($atribute_value_query)) 
					{		
						if(!empty($atribute_value_list["man"])){
							if(!in_array($atribute_value_list["man"], $array_man)){
								$array_man[] = $atribute_value_list["man"];
							}
						}

						$attribute_value_array_one = json_decode($atribute_value_list['attribute'], true);
						if (isset($attribute_value_array_one)&&(!empty($attribute_value_array_one)))
						{
							foreach($attribute_value_array_one as $k => $v)
							{
								if(isset($ajax_filter[$k]))
								{
									if (!in_array($v, $ajax_filter[$k]['value'])&&!empty($v)) 
									{
										$ajax_filter[$k]['value'][] = $v;			
									}	
								}
							}
						}
					}
				}

				//производители
				$whereMan = "";
				if(count($array_man) > 0){
					$array_man = implode(", ", $array_man);
					$whereMan = "WHERE cm.id IN(".$array_man.")";
				}
				//$whereMan = "";
				$query = $db->query("SELECT cm.id, cm.name FROM ".DB_PREFIX."_catalog_manufacture as cm ".$whereMan."");
				echo '<p class="filterItem">Производитель</p><ul data-id="man" style="display: none;" class="filterUl">'; 
					while($r = $db->getRow($query)){
						echo '<li>
							<div class="checkbox">
								<label>
									<input class="form-check-input checkFilterItem" type="checkbox" value="'.$r["id"].'" data-value="'.$r["id"].'" name="man[]">'.$r["name"].'
								</label>
							</div>
						</li>';
					}
				echo '</ul>';

				foreach($ajax_filter as $k => $v)
				{
					if(count($v['value']) > 0){
						echo '<p class="filterItem">'.$v['title'].'</p><ul data-id="'.$k.'" style="display: none;" class="filterUl">';
						asort($v['value']);
						foreach($v['value'] as $kk => $vv)
						{
							echo '<li><div class="checkbox"><label><input class="form-check-input checkFilterItem" type="checkbox" value="'.$vv.'" data-value="'.$vv.'" name="attribute['.$k.'][]">'.$vv.'</label></div></li>';
						}
						echo '</ul>';
					}
					
				}
			} //else echo 'Фильтры отсутствуют!';
        } else echo 'Не хватает данных';
	}
	
	/*
	function getFilter(){
		global $core, $db, $cats, $allowFuncs;
		if(isset($_POST["cat"])){
			$cat = $_POST["cat"];
			$query = $db->query("SELECT * FROM JMY_catalog_atribute WHERE cat LIKE '%,".$cat.",%' AND type = 1");
			if($db->numRows($query) > 0){
				while($r = $db->getRow($query)){
					$arr = $r["value"];
					$arr = explode(",", $arr);
					echo '<p class="filterItem">'.$r["title"].'</p><ul data-id="'.$r["id"].'" style="display: none;">';
					foreach($arr as $v){
						if(!empty($v)){
							$qq = $db->query("SELECT id FROM JMY_catalog WHERE category LIKE ',".$cat.",' AND attribute LIKE '%\"".$r["id"]."\":\"".$v."\"%'");
							if($db->numRows($qq) > 0){
								echo '<li><div class="checkbox"><label><input class="form-check-input checkFilterItem" type="checkbox" value="1" data-value="'.$v.'">'.$v.'</label></div></li>';
							} 
						}
					}
					echo '</ul>';
				}
			} else echo 'Фильтры отсутствуют!';
				
        } else echo 'Не хватает данных';
	}
	*/
    
    /*******fastBar*******/
	
	function fastBarUniversalSave(){
        global $core, $db, $cats, $allowFuncs;
        if(isset($_POST["data"])){
            $data = $_POST["data"];
            $const_name = [
                "sales_detail" => "sales_id",
                "supply_detail" => "sid",
                "supply_order_detail" => "supply_o",
                "selection_detail" => "selection_id",
                "metering_detail" => "metering_id",
                "catalog_price_detail" => "price_id",
                "boiler_bid_data_detail" => "bbd_id",
            ];
            if(strlen($data) > 0){
				$data = json_decode($data, true);
				if($data["module_copy"] == "selection_detail" && $data["module_insert"] == "sales_detail"){
					foreach($data["save"] as $k => $v){
					
						$query = $db->query("SELECT * FROM JMY_selection_detail WHERE id = '".$data["save"][$k]["id"]."' LIMIT 1");
						$r = $db->getRow($query);
						$query = $db->query("SELECT c.* FROM JMY_catalog as c WHERE c.id = '".$r["cid"]."' LIMIT 1");
						$catalog = $db->getRow($query);
						if(!empty($r["id"])){
							$db->query("INSERT INTO JMY_sales_detail (`sales_id`, `cid`, `s_sku`, `s_name`, `price_type`, `s_unit`, `count`, `type_nds`, `status`) VALUES ('".$data["id"]."', '".$r["cid"]."', '".$catalog["sku"]."', '".$catalog["title"]."', '4', '".$catalog["unit"]."', '".$r["count"]."', '3', '1')");
						}
					}
				}else {
						$count = 0;
						foreach($data["save"] as $k => $v){

						if(empty($data["save"][$k]["id"])){
							continue;
						}

						$query = $db->query("SELECT * FROM JMY_".$data["module_copy"]." WHERE id = '".$data["save"][$k]["id"]."' LIMIT 1");
						$r = $db->getRow($query);
						if($data["module_copy"] == $data["module_insert"]){
							$check = $r;
						} else {
							$query = $db->query("SELECT * FROM JMY_".$data["module_insert"]." LIMIT 1");
							$check = $db->getRow($query);
						}
						$str = "";
						$column_name = "";
						foreach($r as $key => $val){
							$flag = true;
							if(array_key_exists($key, $check)){
								switch($key){
									default:
									if((empty($val) && $val != 0) || is_null($val)){
										// $str .= "null, ";
										$flag = false;
									} else {
										$str .= "'".htmlspecialchars($val, ENT_QUOTES)."', ";
									}
									break;
									case $const_name[$data["module_insert"]]:
										$flag = false;
									break;
									case "id":
										$flag = false;
									break;
									case "status":
										$str .= "'".$data["status"]."', ";
									break;
									case "count_realization":
										$flag = false;
									break;
									case "count_assembly":
										$flag = false;
									break;
									case "del":
										$flag = false;
									break;
									case "supply_detail_id":
										$flag = false;
									break;
									case "date_post":
										$flag = false;
									break;
									case "status_history":
										$str .= "'[]', ";
									break;
								}
								if($flag){
									$column_name .= $key.", ";
								}
							}
						}
						//$count++;
						//echo $count;
						$str .= "'".$data["id"]."', ";
						$column_name .= $const_name[$data["module_insert"]].", ";
						$str = mb_substr($str, 0, -2);
						$column_name = mb_substr($column_name, 0, -2);
						//var_dump("INSERT INTO JMY_".$data["module_insert"]." (".$column_name.") VALUES (".$str.")");
						$db->query("INSERT INTO JMY_".$data["module_insert"]." (".$column_name.") VALUES (".$str.")");
					}
					//var_dump($data["save"]);
					if($data["module_insert"] == "sales_detail"){
						\Sales\Support\SalesDetailSupport::recount_sales($data["id"]);
					}
					if($data["module_insert"] == "boiler_bid_data_detail"){
						require_once(ROOT."usr/modules/boiler/admin/sub/bid_data_detail.php");
						change_all_price_boiler_bid($data["id"]);
					}
				}
                
            }
        }
	}

function clipboard_($action){
	$request = isset($_POST["data"]) ? json_decode($_POST["data"], true) : false;
	if($request){
		$module_copy_name = Clipboard::parse_name($request["module_copy"]);
		$module_insert_name = Clipboard::parse_name($request["module_insert"]);
		$cx_module_copy = class_exists($module_copy_name);
		$cx_module_insert = class_exists($module_insert_name);
		if($cx_module_copy && $cx_module_insert){
			$module_copy = new $module_copy_name($request);
			$module_insert = new $module_insert_name($request);

			$module_insert->module_copy = $request["module_copy"];
			$module_insert->module_insert = $request["module_insert"];

			$universal_data = $module_copy->convert_to();
			$insert_data = $module_insert->convert_from($universal_data);
			// dd($universal_data);
			// var_dump($module_insert);
			// exit;
			$action($module_insert, $insert_data);
			
		} else {
			if(!$cx_module_copy){
				echo 'Отсутствует модуль копирования - "'.$request["module_copy"].'"; ';
			}
			if(!$cx_module_insert){
				echo 'Отсутствует модуль вставки - "'.$request["module_insert"].'"; ';
			}
		}
	}
}

function clipboard(){
	clipboard_(function($module_insert, $insert_data){
		$module_insert->insert($insert_data);
	});
}

function clipboard_data(){
	clipboard_(function($module_insert, $insert_data){
		$data = $module_insert->insert_items($insert_data);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	});
}

function oSelectUniversalAjax($get, $request){
	//["search" => function($val){return "()";}, "db_name" => "", "items_template" => function($row){ return [];}]
	//"where" => ""
	//"order_by_type" => ""
	//"order_by_field" => ""
	//"query" => function($request){//$str, $pageCount}
	//"query_total_count" => function($request){//$str}
	global $db;
	if(isset($get["q"])){
		$search_func = $request["search"];
		$db_name = $request["db_name"];
		$items_template_func = $request["items_template"];

		$str = "TRUE";
		if(!empty($get["q"])){
			$mas = explode(" ", $get["q"]);
			$str = [];
			foreach($mas as $val){
				if(!empty($val)) $str[] = $search_func($val);
			}
			if(!empty($str)){
				$str = implode(" AND ", $str);
				$str = "(".$str.")";
			} else {
				$str = "TRUE";
			}
		}
		
		if(!empty($request["where"])){
			$str .= " AND ".$request["where"];
		}

		$page = isset($get["page"]) ? ($get["page"] - 1) : 0;
		$pageCountFixed = 30;
		$pageCount = $page * $pageCountFixed;

		if(empty($request["query_total_count"])){
			$total_count_query = "SELECT * FROM `".$db_name."` WHERE ".$str;
		} else {
			$total_count_query_func = $request["query_total_count"];
			$total_count_query = $total_count_query_func(["str" => $str, "get" => $get]);
		}

		$total_count = $db->query($total_count_query);
		
		if(empty($request["query"])){
			$order_type = empty($request["order_by_type"]) ? "ASC" : $request["order_by_type"];
			$order_by = empty($request["order_by_field"]) ? "" : "ORDER BY ".$request["order_by_field"]." ".$order_type;
			$query = $db->query("SELECT * FROM `".$db_name."` WHERE ".$str." ".$order_by." LIMIT ".$pageCount.", 30");
		} else {
			$query_func = $request["query"];
			$q = $query_func(["str" => $str, "pageCount" => $pageCount, "pageCountFixed" => $pageCountFixed, "get" => $get]);
			$query = $db->query($q);
		}
		
		$json = [];
		$json["total_count"] = $db->numRows($total_count);
		$json["incomplete_results"] = false;
		$json["items"] = [];
		while($row = $db->getRow($query)){
			$json["items"][] = $items_template_func($row);
		}
		echo json_encode($json);
	}
}
	
function getOrganizations(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`name` LIKE '%".$val."%' OR `inn` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_organizations", 
		"order_by_field" => "`name`",
		"items_template" => function($row){
			return ['id'=>$row['id'], 'text'=>$row['name']." (".$row["inn"].")"];
		}
	]);
}

function getBoiler(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`title` LIKE '%".$val."%' OR `numb` LIKE '%".$val."%' OR `id` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_boiler_bid", 
		"order_by_field" => "`title`",
		"items_template" => function($row){
			return ['id'=>$row['id'], 'text'=>$row['numb']." (".$row["title"].")"];
		}
	]);
}

function getOrgToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(o.name LIKE '%".$val."%' OR o.inn LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_org", 
		"query_total_count" => function($request){
			$str = $request["str"];
			return "SELECT org.id FROM 
			`".DB_PREFIX."_org` as org 
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(org.oid = o.id) 
			WHERE ".$str;
		},
		"query" => function($request){
			$str = $request["str"];
			$pageCount = $request["pageCount"];
			$pageCountFixed = $request["pageCountFixed"];

			return "SELECT org.*, o.name, o.inn
			FROM `".DB_PREFIX."_org` as org
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(org.oid = o.id) 
			WHERE ".$str." ORDER BY org.id DESC LIMIT ".$pageCount.", ".$pageCountFixed;
		},
		"items_template" => function($row){
			return ['id'=>$row['id'], 'text'=>$row['name']." (".$row["inn"].")"];
		}
	]);
}

function getObjectToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`name` LIKE '%".$val."%' OR `id` LIKE '".$val."' OR `address` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_object", 
		"order_by_field" => "`name`",
		"items_template" => function($row){
			return ['id'=>$row['id'], 'text'=>"#".module_perfix("object", $row['id'])." ".$row['name'].(empty($row["address"]) ? "" : " (".$row["address"].")")];
		}
	]);
}

function getUsersMain($where){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`surname` LIKE '%".$val."%' OR `name` LIKE '%".$val."%' OR `ochestvo` LIKE '%".$val."%' OR `id` LIKE '".$val."')";
		}, 
		"db_name" => DB_PREFIX."_users", 
		"where" => $where." AND `hidden` = '0'",
		"order_by_field" => "`surname`",
		"items_template" => function($row){
			return ['id'=>$row['id'], 'text'=> $row["surname"]." ".$row["name"]." ".$row["ochestvo"] ];
		}
	]);
}

function getUsersToSelect(){
	getUsersMain("`group` = '1'");
}

function getUsersAllToSelect(){
	getUsersMain("`active` = '1'");
}

function getSalesToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`numb` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_sales", 
		"query" => function($request){
			$str = $request["str"];
			$pageCount = $request["pageCount"];
			$pageCountFixed = $request["pageCountFixed"];

			return "SELECT s.*, o.name 
			FROM `".DB_PREFIX."_sales` as s 
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(s.oid = o.id) 
			WHERE ".$str." ORDER BY s.id DESC LIMIT ".$pageCount.", ".$pageCountFixed;
		},
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["numb"].' '.$row["name"]];
		}
	]);
}

function getRealizationToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`numb` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_realization", 
		"query" => function($request){
			$str = $request["str"];
			$pageCount = $request["pageCount"];
			$pageCountFixed = $request["pageCountFixed"];

			return "SELECT r.*, o.name 
			FROM `".DB_PREFIX."_realization` as r 
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(r.oid = o.id) 
			WHERE ".$str." ORDER BY r.id DESC LIMIT ".$pageCount.", ".$pageCountFixed;
		},
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["numb"].' '.$row["name"]];
		}
	]);
}

function getSupplyOrderToSelect(){
	$getOrg = function($request){
		$org = $request["get"]["org"] ?? "";
		if(!empty($org)){
			$org = "so.org = '{$org}' AND ";
		}
		return $org;
	};
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(so.id LIKE '".$val."' OR so.numb LIKE '%".$val."%' OR o.name LIKE '%".$val."%' OR so.bill_code LIKE '".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_supply_order", 
		"query_total_count" => function($request) use ($getOrg){
			$str = $request["str"];
			$org = $getOrg($request);
			return "SELECT so.id FROM 
			`".DB_PREFIX."_supply_order` as so 
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(so.oid = o.id) 
			WHERE ".$org.$str;
		},
		"query" => function($request) use ($getOrg){
			$str = $request["str"];
			$pageCount = $request["pageCount"];
			$pageCountFixed = $request["pageCountFixed"];
			$org = $getOrg($request);
			return "SELECT so.*, o.name 
			FROM `".DB_PREFIX."_supply_order` as so 
			LEFT JOIN `".DB_PREFIX."_organizations` as o ON(so.oid = o.id) 
			WHERE ".$org.$str." ORDER BY so.id DESC LIMIT ".$pageCount.", ".$pageCountFixed;
		},
		"order_by_field" => "`id`",
		"order_by_type" => "DESC",
		"items_template" => function($row){
			return [
				'id' => $row['id'], 
				'text' => $row["numb"].' '.$row["name"].' '.(empty($row["bill_code"]) ? '' : '('.$row["bill_code"].')'),
			];
		}
	]);
}

function getCatalogManufactureToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(id LIKE '".$val."' OR name LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_catalog_manufacture", 
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["name"]];
		}
	]);
}

function getCatalogToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(id LIKE '".$val."' OR title LIKE '%".$val."%' OR sku LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_catalog",
		"where" => "hidden = 0", 
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => "(".$row["sku"].") - ".$row["title"]];
		}
	]);
}

function getOrganizationsBankToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(id LIKE '".$val."' OR name_short LIKE '%".$val."%' OR name LIKE '%".$val."%' OR rschet LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_organizations_bank",
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["rschet"].', '.(empty($row["name_short"]) ? $row["name"] : $row["name_short"])];
		}
	]);
}

function getMergingToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(id LIKE '".$val."' OR numb LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_merging",
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["numb"]."({$row['name']} - {$row['id']})"];
		}
	]);
}

function getCatalogGroupsToSelect(){
	oSelectUniversalAjax($_GET, [
		"search" => function($val){
			return "(`id` LIKE '".$val."' OR `name` LIKE '%".$val."%')";
		}, 
		"db_name" => DB_PREFIX."_catalog_groups",
		"items_template" => function($row){
			return ['id' => $row['id'], 'text' => $row["name"]];
		}
	]);
}

function add_message_chat(){
	global $allowFuncs, $db;
	if(isset($_POST["chat_id"], $_POST["message"])){
		$chat = new Chat();
		echo $chat->add_message($_POST);
	}
}

function notif_send_message(){
	global $allowFuncs, $db;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : false;
	$message_id = isset($_POST["message_id"]) ? $_POST["message_id"] : null;
	$chat = new Chat();
	$chat->notif_send_message($chat_id, $message_id);
}

function load_chat_list(){
	global $allowFuncs, $db, $core;
	$data = $_POST;
	$chat = new Chat();
	$lists = $chat->chat_list($data);
	$chat->load_chat_list($lists);
}

function load_chat_message(){
	global $allowFuncs, $db, $core;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : null;
	$limit = isset($_POST["limit"]) ? $_POST["limit"] : null;
	$message_id = isset($_POST["message_id"]) ? ($_POST["message_id"] === "false" ? false : $_POST["message_id"]) : false;
	$chat = new Chat();
	$chat->load_chat_message($chat_id, $message_id, "echo", $limit);
}

function load_users_list(){
	global $db;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : null;
	$chat = new Chat();
	$chat->load_users_list($chat_id);
}

function chat_save_file(){
	global $db;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : false;
	$file = isset($_FILES["file"]) ? $_FILES["file"] : false;

	$chat = new Chat();
	$message_id = $chat->add_message(["chat_id" => $chat_id, "websocket" => false]);
	$aiid = attach_save_file($file, $message_id, "chat", "chat");
	$db->query("UPDATE `".DB_PREFIX."_chat_message` SET file = '".$aiid."' WHERE id = '".$message_id."' LIMIT 1");
	$chat->send_websocket($chat_id, $message_id);
	echo $message_id;
}

function read_chat_message(){
	global $db;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : false;
	if($chat_id){
		$chat = new Chat();
		$chat->read_chat_message($chat_id);
	}
}

function change_uid_chat(){
	global $db;
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : false;
	$id = isset($_POST["id"]) ? $_POST["id"] : [];
	$status = isset($_POST["status"]) ? $_POST["status"] : "";

	$chat = new Chat();
	switch($status){
		case "true":
			$chat->add_users($chat_id, $id);
		break;
		case "false":
			$chat->del_users($chat_id, $id);
		break;
	}
}

function find_chat(){
	global $db;
	$module = isset($_POST["module"]) ? $_POST["module"] : false;
	$pid = isset($_POST["pid"]) ? $_POST["pid"] : false;
	if($module && $pid){
		$end_module = false;
		$end_pid = false;
		if($module !== "sales"){
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_".$module." WHERE id = '".$pid."' LIMIT 1");
			if($db->numRows($query) > 0){
				$r = $db->getRow($query);
				if(!empty($r["sales_id"])){
					$end_module = "sales";
					$end_pid = $r["sales_id"];
				} else {
					$end_module = $module;
					$end_pid = $pid;
				}
			} else {
				$end_module = $module;
				$end_pid = $pid;
			}
		} else {
			$end_module = $module;
			$end_pid = $pid;
		}
		if($end_module && $end_pid){
			$chat = new Chat();
			$chat_array = $chat->find_chat(["once" => true, "where" => ["module" => $end_module, "pid" => $end_pid, "type" => 1, "uid" => ""]]);
			echo json_encode($chat_array, JSON_UNESCAPED_UNICODE);
		}
	}
}

function find_chat_all(){
	global $db;
	$data = $_POST;
	$chat = new Chat();
	$chat_array = $chat->find_chat(["where" => $data]);
	echo json_encode($chat_array, JSON_UNESCAPED_UNICODE);
}

function find_and_load_chat_lists(){
	global $db;
	$data = $_POST;
	$chat = new Chat();
	$lists = $chat->find_chat(["where" => $data]);
	$chat->load_chat_list($lists);
}

function check_chat_site(){
	$chat_id = isset($_POST["chat_id"]) ? $_POST["chat_id"] : false;
	$chat = new Chat();
	$data = $chat->check_site($chat_id);
	if($data){
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}

function get_chat_data(){
	$chat_id = $_POST["chat_id"] ?? false;
	if($chat_id){
		$chat = new Chat();
		$chat_data = $chat->find_chat(["once" => true, "where" => ["id" => $chat_id,]]);
		$chat_data["link"] = $chat->module_to_link($chat_data["module"], $chat_data["pid"]);
		echo json_encode($chat_data, JSON_UNESCAPED_UNICODE);
	}
}

function change_chat(){
	$id = isset($_POST["id"]) ? $_POST["id"] : false;
	$field = isset($_POST["field"]) ? $_POST["field"] : false;
	$value = isset($_POST["value"]) ? $_POST["value"] : false;
	$chat = new Chat();
	$chat->change_chat($id, $field, $value);
}

function load_list_union(){
	$data = isset($_POST["data"]) ? $_POST["data"] : false;
	if($data){
		echo json_encode(get_repeat_tov($data));
	}
}

function get_all_no_read_message_count(){
	$chat = new Chat();
	echo $chat->get_all_no_read_message_count();
}
	
function relationsAddAjax(){
	$data = $_POST;
	$relations = new Relations;
	$relations->add($data);
}
	
function relationsDeleteAjax(){
	$data = $_POST;
	$relations = new Relations;
	$relations->delete($data);
}

function relationsFindAjax(){
	$data = $_POST;
	$relations = new Relations;
	echo json_encode($relations->find($data), JSON_UNESCAPED_UNICODE);
}
	
function get_org_data_ajax(){
	echo json_encode(get_org_data());
}

function setPushToken(){
	global $core, $db;
	$uid = $core->auth->user_info['id'];
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		header('HTTP/1.1 400 Bad Request');
		exit;
	}
	$token = '';
	if(isset($_POST['token'])&&!empty($_POST['token']))
	{
		$token = $_POST['token'];
		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "_users_token` WHERE uid = '".$uid."' AND token ='".$token."' AND platform = 'push'");
        if($db->numRows($query) <= 0)
		{
			$db->query("INSERT INTO ".DB_PREFIX."_users_token (`id`, `uid`, `token`, `platform`, `device_id`, `date`, `veritification_code`, `status`) VALUES (NULL, '".$uid."', '".$token."', 'push', '0', '".date('Y-m-d H:i:s')."', '0', '1')");
		}
	}		
}

function selected_find(){
	$request = $_POST;
	if(isset($request["class_name"]) && class_exists($request["class_name"])){
		$class = new $request["class_name"]($request);
		echo $class->view_finds_body();
	}
}

function add_chat(){
	$request = $_POST;
	$chat = new Chat;
	echo $chat->create__chat($request);
}

function get_ftp_file(){
	$request = $_GET;
	$id = isset($request["id"]) ? $request["id"] : false;
	if($id){
		$ftp = new StorageFTP;
		$ftp->get_file_id($id);
	}
}

function request_god(){
	global $core;
	$request = $_POST;

	$uid = $core->auth->user_info['id'];
	$module = $request["module"];
	$pid = $request["pid"];

	$attr = [];
	$attr['uid'] = 3;
	$attr['title'] = 'Запрос на проведение в минус!';
	$attr['text'] = "Запрос на проведение в минус ${module} ${pid}";
	$attr['icon'] = config('app.url').'/files/push/new_order.png';
	$attr['url'] = "https://atri-energo.ru/administration/god&module=${module}&pid=${pid}&uid=${uid}";
	$attr['pNotif'] = true;
	$attr['pPush'] = true;
	$attr['module'] = "god";
    $attr['pid'] = 0;
	sendAllNotif($attr);
}

function victoria_load_list(){
	$victoria = new \Victoria;
	$data = $victoria->grid_items_body($_POST);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function victoria_get_item(){
	$victoria = new \Victoria;
	$data = $victoria->grid_items_basket_body($_POST);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function victoria_filter(){
	$victoria = new \Victoria;
	$data = $victoria->filter($_POST);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function victoria_cert(){
	$victoria = new \Victoria;
	$data = $victoria->cert($_POST);
	echo json_encode($data, JSON_UNESCAPED_UNICODE); 
}

function victoria_attribute(){
	$victoria = new \Victoria;
	$data = $victoria->attribute($_POST);
	echo json_encode($data, JSON_UNESCAPED_UNICODE); 
}

function extendedSortGetCategory(){
	$extendedSort = new \ExtendedSort;
	$data = $extendedSort->get_category();
	echo json_encode($data, JSON_UNESCAPED_UNICODE); 
}

function extendedSortTemplateSave(){
	$extendedSort = new \ExtendedSort;
	$extendedSort->template_add($_POST);
}

function extendedSortTemplateGet(){
	$extendedSort = new \ExtendedSort;
	$data = $extendedSort->template_list();
	echo json_encode($data, JSON_UNESCAPED_UNICODE); 
}

function extendedSortTemplateDelete(){
	$request = $_POST;
	$id = empty($request["id"]) ? false : $request["id"];
	\Boot\Models\ExtendedSortTemplate::destroy($id);
}

function getPriceTypeSelectData(){
	echo json_encode(\PriceTypeSelect::getPrice($_POST), JSON_UNESCAPED_UNICODE);
}