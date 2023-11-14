<?php
    function economyBlock($id = null)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		if(isset($id))
		{	?>
			<div class="panel-heading">
				<span class="panel-icon"><i class="far fa-file-invoice-dollar"></i></span>
				<span class="panel-title"> Экономические показатели</span>
			</div>
			<div class="panel-body bg-light pt20 pbn">					
				<div class="checkbox-custom fill checkbox-primary mb15">
					<input id="allEconomy" type="checkbox">
					<label onclick="economyBlockList('<?php echo $id; ?>', 'allEconomy');" for="allEconomy">Все</label>
					<input id="salesEconomy" type="checkbox" checked="">
					<label onclick="economyBlockList('<?php echo $id; ?>', 'salesEconomy');" for="salesEconomy">Заказы</label>
					<input id="realizationEconomy" type="checkbox">
					<label onclick="economyBlockList('<?php echo $id; ?>', 'realizationEconomy');" for="realizationEconomy">Реализации</label>
					<input id="receiptsEconomy" type="checkbox">
					<label onclick="economyBlockList('<?php echo $id; ?>', 'receiptsEconomy');" for="receiptsEconomy">Поступления</label>
					<input id="transferEconomy" type="checkbox" checked="">
					<label onclick="economyBlockList('<?php echo $id; ?>', 'transferEconomy');" for="transferEconomy">Перемещения</label>
				</div>		
			</div>
			<div class="panel-body" id="economyBlockList">
			
			</div>
			<div class="panel-body bg-light pt20 pbn text-right">	
			<div class=" mb15"><b>Итого:</b><div id="economySum"></div></div>
			</div>
<?php 	}
	}
	
	
	
	function economyBlockList($id = null)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		$id = intval($_POST["id"]);
		$type = $_POST["type"];
		//print_r($type);
		$i = 0;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_relations WHERE (module_out = 'object'  AND pid_out = '".$id."') OR (module_in = 'object'  AND pid_in = '".$id."') ORDER BY id DESC");	
			if($db->numRows($query) > 0)
		{
	
			while($object = $db->getRow($query))
			{
				$i++;
				if($object['module_out'] == 'object')
				{
					$mmm =  $object['module_in'];
					$iid = $object['pid_in'];
				}
				else
				{
					$mmm =  $object['module_out'];
					$iid = $object['pid_out'];
				}
				//print_r($object);
				switch($mmm) 
				{
					case "transfer":
						if(strripos($type, 'transferEconomy') !== false)
						{
							$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer WHERE id = '" . $iid . "'");
							$r = $db->getRow($queryR); 
							$querySum = $db->query("SELECT count, s_price FROM `".DB_PREFIX."_stock_transfer_detail` WHERE `transfer_id` = '".$r['id']."'");
							$total = 0;
							if($db->numRows($querySum) > 0)
							{
								while($rSum = $db->getRow($querySum)) 
								{
									$total = $total + $rSum['s_price']*$rSum['count'];
								}
							}
							
							
							?>
							<div class="panel panel-default" style="margin-bottom: -1px;">
								<div class="panel-heading" role="tab" id="headingOne" style="padding: 10px 10px 10px; overflow:visible;">
									<h4 class="panel-title" style="font-size: 16px;">
										<a role="button" data-toggle="collapse" href="#collapse_<?php echo $r['id'];?>" aria-expanded="true" aria-controls="collapse_<?php echo $r['id'];?>" class="trigger collapsed">Перемещение #<?php echo module_perfix('storage_transfer', $r['id']);?></a>
										
					<a data-total="	<?php echo $total;?>" href="<?php echo ADMIN; ?>/module/storage/transfer/detail/<?php echo $r['id'];?>" target="_blank" class="text-right rowTotal pull-right">
					<?php echo $total;?>
					</a>
					</h4>	
					
					<div style="line-height: 20px; margin-top: 10px;">
					<?php
			
					
					echo '</div> 
					
					</div>
					<div id="collapse_'.$r['id'].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
					<div class="panel-body" style="padding: 0px;">
					<table class="table table-striped" id="tablesForm">
                    <thead>
					<tr>
					<th class="w40"><span class="pd-l-sm"></span>#</th>
					<th class="w200">Номенклатура</th>
					<th class="w150">Артикул</th>
					<th class="w150">Количество</th>	
					<th class="w150">Цена</th>
					<th class="w200">Сумма</th>				
					
					</tr>
                    </thead>
                    <tbody id="tov_list">					
					
					';
					
					
					$queryPN = $db->query("SELECT s.* FROM ".DB_PREFIX."_stock_transfer_detail as s WHERE s.transfer_id = '".$r['id']."'");
					
					if($db->numRows($queryPN) > 0)
					{	
						$i=0;
						$total = 0;
						while($content = $db->getRow($queryPN)) 
						{
							$i++;
							$flag = true;
							$total = $total +  $content['s_price']*$content['count'];
						
							
							//' . $dn_array[$content['dn']] . '
							
							echo '
							<tr>
							<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
							<td>' . $content['s_name'] . '</td>
							<td>' . $content['s_sku'] . '</td>
							<td>' . $content['count'] . '</td>	
							<td>' . $content['s_price']. ' рублей.</td>					
							<td>' . $content['s_price']*$content['count']. ' рублей.</td>	
							
							
							</tr>';		
						}
						echo '<tr>
						<td colspan="5" class="text-right"><b>Итого:</b></td>
						<td colspan="2" class="text-left"><b>'.$total.' рублей.</b></td>
						</tr>';		
						
					} 	
					else 
					{
						echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
					}	
					
					
					
					
					
					
					
					echo '	</tbody>
					
					</table> 
					</div>
					</div>
					</div>
					
					
					
					
					
					';
							
						}
					break;
					case "sales":
					if(strripos($type, 'salesEconomy') !== false)
						{
					$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $object['pid_in'] . "'");
					$task = $db->getRow($queryR);
					
					echo '
					
					<div class="panel panel-default" style="margin-bottom: -1px;">
					<div class="panel-heading" role="tab" id="headingOne" style="padding: 10px 10px 10px; overflow:visible;">
					<h4 class="panel-title" style="font-size: 16px;">
					<a role="button" data-toggle="collapse" href="#collapse_'.$task['id'].'" aria-expanded="true" aria-controls="collapse_'.$task['id'].'" class="trigger collapsed">
					Заказ PN-'.$task['id'].' ('.$task['ytid'].') от ('.$task['date'].') 
					</a>
					— <span class="changeTitleSales" style="cursor: pointer; border-bottom: 1px black dashed; '.(empty($task["title"]) ? "font-style: italic; color: #DD1144;" : "").'" data-id="'.$task['id'].'">'.(empty($task["title"]) ? 'Отсутствует' : $task["title"]).'</span> 
					<a id="rowTotal" data-total="'.$task['total'].'" href="'.ADMIN.'/module/sales/order/'.$task['id'].'" target="_blank" class="text-right pull-right rowTotal">
					'.$task['total'].'
					</a>
					</h4>	
					
					<div style="line-height: 20px; margin-top: 10px;">';
					$zzz = $db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'sales' AND c.module_id = '".$task['id']."'");	
					
					$p = 1;
					while($zz = $db->getRow($zzz))
					{
						if($p === 2){
							echo '<div class="hideAllToTwo">';
						}
						echo '<div>
						<i class="fa fa-comment-o" aria-hidden="true"></i>
						<span>'.$zz['date'].' '.$zz['surname'].' '.mb_substr($zz['name'], 0, 1).'. '.mb_substr($zz['ochestvo'], 0, 1).'.'.'</span>
						<span><i>'.$zz['text'].'</i></span>
						<div>';
						$queryTC = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE pid = '".$zz['id']."' AND `module` = 'sales'");
						if($db->numRows($queryTC) > 0){
							while($myr = $db->getRow($queryTC)){
								echo '<a target="_blank" href="/download/'.$myr['id'].'">'.$myr['name'].'</a><br>';
							}
						}
						echo '</div>
						</div>';
						$p++;
					}
					$p--;
					if($p > 1){
						echo '</div><a href="javascript:void(0);" class="showHideAllToTwo">Показать все</a>';
					}
					
					echo '</div> 
					
					</div>
					<div id="collapse_'.$task['id'].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
					<div class="panel-body" style="padding: 0px;">
					<table class="table table-striped" id="tablesForm">
                    <thead>
					<tr>
					<th class="w40"><span class="pd-l-sm"></span>#</th>
					<th class="w200">Номенклатура</th>
					<th class="w150">Артикул</th>
					<th class="w150">Количество</th>							
					<th class="w150">Вид цены</th>
					<th class="w150">Цена</th>
					<th class="w200">Скидка</th>
					<th class="w200">Сумма</th>
					<th class="w40 text-center"><span class="fa fa-pencil fa-lg"></span></th>		
					<th class="w40 text-center">' . $lang['status'] .'</th>						
					
					</tr>
                    </thead>
                    <tbody id="tov_list">					
					
					';
					
					
					$queryPN = $db->query("SELECT s.*, d.title, p.price FROM ".DB_PREFIX."_sales_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS d on(d.id=s.cid) LEFT JOIN ".DB_PREFIX."_ver_device_price as p on(d.id=p.vdid) WHERE s.sales_id = '".$task['id']."' AND s.status != '0'");
					
					if($db->numRows($queryPN) > 0)
					{	
						$i=0;
						$total = 0;
						while($content = $db->getRow($queryPN)) 
						{
							$i++;
							$flag = true;
							
							if ($content['status'] == 1)
							{
								//print_r($content['status']);
								$status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title="Прибор проверяется"></span>';
								$flag = false;
							}
							elseif ($content['status'] == 2)
							{
								$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
							}			
							elseif ($content['status'] == 3)
							{
								$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
							}
							$total = $total + ($content['total']);
							
							//' . $dn_array[$content['dn']] . '
							
							echo '
							<tr>
							<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
							<td>' . $content['s_name'] . '</td>
							<td>' . $content['s_sku'] . '</td>
							<td>' . $content['count'] . '</td>				
							<td>0</td>
							<td>' . $content['s_price']. ' рублей.</td>		
							<td>' . $content['discount']. '%</td>				
							<td>' . $content['total']. ' рублей.</td>	
							<td>'.(!empty($content['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-'.$content['id'].'\')" class="fa fa fa-edit text-info fa-md"></span>
							<div id="modal-comm-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
							<div class="panel-body">
							<h3 class="mt5">Комментарий:</h3>					
							<hr class="short alt">
							<p>'.$content['comment'].'</p>
							</div>
							<div class="panel-footer text-right">
							
							</div>
							</div>
							</div>' : '<span class="fa fa fa-edit fa-md"></span>').'</td>					
							<td class="text-center">' . $status_icon . '</td>
							
							</tr>';		
						}
						echo '<tr>
						<td colspan="7" class="text-right"><b>Итого:</b></td>
						<td colspan="5" class="text-left"><b>'.$total.' рублей.</b></td>
						</tr>';		
						
					} 	
					else 
					{
						echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
					}	
					
					
					
					
					
					
					
					echo '	</tbody>
					
					</table> 
					</div>
					</div>
					</div>
					
					
					
					
					
					';
						}
					break;
					
				}
			}
		}
	}
	