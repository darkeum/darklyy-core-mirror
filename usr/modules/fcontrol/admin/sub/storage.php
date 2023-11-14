<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function content_storage() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = $lang['fcontrol_control'];
	$adminTpl->admin_head($lang['fcontrol'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'fcontrol';
	$atributs['module_open'] = 'storage_detail';
	load_grid('jqFcontrolStorage', $lnn, $atributs, ROOT.'/usr/modules/fcontrol/admin/grid/');
    //$atributs['js'] = '  setTimeout(function () {$("#jqFcontrolStorage").jqGrid(\'setGridParam\',{postData:{status: \'2\'}}); $(\'#jqFcontrolStorage\').trigger(\'reloadGrid\');}, 150);';	
	$adminTpl->admin_foot();
} 

function saveAllStorageForADay(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $date = date("Y-m-d");
    
    $db->query("INSERT INTO JMY_control_storage (`date`, `status`) VALUES ('".date("Y-m-d H:i:s")."', '1')");
    $insert_id = $db->insert_id();
    $q = $db->query("SELECT id FROM JMY_storage WHERE date LIKE '".$date."%' AND status = 2");
    while($r = $db->getRow($q)){
        $qq = $db->query("SELECT id FROM JMY_storage_detail WHERE storage_id = '".$r["id"]."'");
        while($rr = $db->getRow($qq)){
            $db->query("INSERT INTO JMY_control_storage_detail (`control_storage_id`, `storage_id`, `storage_detail_id`, `status`) VALUES ('".$insert_id."', '".$r["id"]."', '".$rr["id"]."', '1')");
        }
    }
}

function storage_detail($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    
	  $rows_status = array();
		$rows_status[0] = 'Отменен';
		$rows_status[1] = 'Ожидает закупки';
      	$rows_status[2] = 'Ожидает отправки';
		$rows_status[3] = 'Закуплено';
	$query = $db->query("SELECT sd.*,sc.title, sc.date as scdate FROM JMY_control_storage as sc LEFT JOIN JMY_control_storage_detail as csd ON(sc.id = csd.control_storage_id) LEFT JOIN JMY_storage_detail as sd ON(sd.id = csd.storage_detail_id) WHERE sc.id = '".$id."'"); 
	$order = $db->getRow($query);		
	$adminTpl->admin_head('Контроль остатков | '.$order['title'].' #'.module_perfix('fcontrol_storage', $id));								
	echo '
    <style>
    .cp{
        cursor: pointer;
    }
    </style>
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead" style="text-transform: uppercase;">'.$order['title'].' #'.module_perfix('fcontrol_storage', $id).'</p>
        </div>	
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-9 pl15">
							<div class="section row mb15">
									<div class="col-md-4">
										<div class="element-groups-lg-custom wizard-options">
											<div class="ph10">
												<a href="/administration/module/fcontrol/storage_print/'.$id.'" class="holder-style p15 text-center holder-active">
													<span class="glyphicon glyphicon-print holder-icon"></span><br>Распечатать <br>заявку
												</a>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<small>Статус:</small><br/>
										<b>'.  $rows_status[$order['status']] .'</b>
									</div>
									<div class="col-md-4">
										<small>Дата:</small><br/>
										<b>'. date("d.m.Y", strtotime($order['scdate'])) .'</b><br/><br/>
                                                    
									</div>
							</div>												
							</div>								
							</div>
						</div>
					
					</div>
				</div>
	<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список приборов:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/storage_detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>												
					</div>	
              </div>
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/storage_detail_list/'.$id.'\', \'dev_list\');</script>
                <script src="/usr/modules/fcontrol/admin/asset/js/controlStorage.js"></script>
              
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>							
							<th class="w50">Ед.</th>
							<th class="w50">Наличие</th>
							<th class="w50">Разница</th>	
							<th class="w40 text-center">' . $lang['status'] .'</th>						
							<th class="w200 text-right">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody id="dev_list">					
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="12">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">							
								<option value="2">Подтвердить</option>
								<option value="3">Отвергнуть</option>								
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
                </form>
			 </div>			
          </div>
        </div>
	</section>';
	$adminTpl->admin_foot();
}

function get_query_detail($id){
	global $db;
	return $db->query("SELECT sd.*, c.*, csd.id as csdid, csd.status as csdstatus, s.stock_id as s_stock_id
	FROM `".DB_PREFIX."_control_storage_detail` as csd 
	LEFT JOIN `".DB_PREFIX."_storage_detail` as sd ON(sd.id = csd.storage_detail_id) 
	LEFT JOIN `".DB_PREFIX."_storage` as s ON(s.id = sd.storage_id) 
	LEFT JOIN `".DB_PREFIX."_catalog` as c ON (c.id = sd.cid) 
	WHERE csd.control_storage_id = '".$id."'");
}

function storage_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang, $dn_array;	
	$query = get_query_detail($id);
//    $query = [];
    // dd($db->numRows($query));ы
	if($db->numRows($query) > 0)
	{	
	$i=0;
	while($content = $db->getRow($query)) 
		{
			$i++;
			$flag = true;
            
            switch($content['csdstatus']){
                default:
                    $status_icon = '';
                break;
                case 1:
                    $status_icon = '<span class="far fa-circle text-danger fa-md"></span>';
                    $flag = false;
                break;
                case 2:
                    $status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
                break;
                case 3:
                    $status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title=""></span>';
                break;
			}
			
			$unit = get_unit('data', $content['unit'] ,'sname');
			// $count_stable = $content['count_stable'];
			$count_stable = get_ndr(["cid" => $content["cid"], "stock_id" => $content["s_stock_id"], "module" => "sales", "pid" => null, "returnType" => "view"]);
			echo '
			<tr data-id="'.$content['csdid'].'">
				<td data-toggle="tooltip" title="' . $content['csdid'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['sku'] . '</td>
				<td>' . $content['title'] . '</td>			
				<td>' . $unit . '</td>		
				<td class="availability">' . $count_stable . '</td>		
				<td class="cp count_now">' . $content["count_now"] . '</td>						
				<td class="text-center">' . $status_icon . '</td>
				<td class="text-right">';
					if($content["csdstatus"] != 2){
						echo '<div class="btn-group" >
                        <button type="button" class="btn btn-xs btn-success btnStatus" data-status="2">Подтвердить</button>
						<button type="button" class="btn btn-xs btn-danger btnStatus" data-status="3">Отвергнуть</button>
                    </div>';
					}
				echo '</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['csdid'] . '" type="checkbox" name="checks[]" value="' . $content['csdid'] . '" class="checks">
						<label for="checkbox' . $content['csdid'] . '"></label>
					</div>
				</td>
			</tr>';
		}
	} 	
	else 
	{
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
	}	
}

function storage_print($id){
	global $db;

	require_once(ROOT."/vendor/autoload.php");
	$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
	$file_title = "storage_".time().".xlsx";
	$tpl = "files/tpl/fcontrol/storage.xlsx";
	$spreadsheet = $reader->load($tpl);

	$fields = [
		"B" => "sku",
		"C" => "title",
		"D" => "count_stable",
		"E" => "count_now",
	];
	$iRow = 2;
	$query = get_query_detail($id);
	$i = 1;
	while($r = $db->getRow($query)){
		for ($iCol = 'A'; $iCol <= 'E'; $iCol++){
			switch($iCol){
				default:
					$value = $r[ $fields[ $iCol ] ];
				break;
				case "A":
					$value = $i;
				break;
			}
			$spreadsheet->setActiveSheetIndex(0)->setCellValue($iCol.$iRow, $value);
		}
		$i++;
		$iRow++;
	}

	$oWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$file_title); /*-- $filename is  xsl filename ---*/
	header('Cache-Control: max-age=0');
	$oWriter->save('php://output');
}