<?php
	
	if(isset($syncArray['stock_transfer']))
	{

		//echo '<pre>';
		//Обработчик перемещений		
		foreach ($syncArray['stock_transfer'] as $key => $value)
		{
			$query_transfer = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer WHERE id = '".$value['pid']."'");		
            if($db->numRows($query_transfer) > 0) 
            {
				$transfer = $db->getRow($query_transfer);	
				$r_numb = $transfer['numb'];
				if(!empty($transfer['object']))
				{
					$query_object = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '".$transfer['object']."'");		
					if($db->numRows($query_object) > 0) 
					{
						$object = $db->getRow($query_object);	
						$object_name = '';	
						if(!empty($object['name']))
						{
							$object_name = $object['name'];	
						}
					}
				}
				//получение ключей единиц измерения
				$query_relation_unit = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'unit' AND sync_base='".$id."'");
				$unitRefArray =array();
				if($db->numRows($query_relation_unit) > 0) 
				{					
					while($u = $db->getRow($query_relation_unit))
					{	
						$unitRefArray[$u['pid']] = $u['id_1c'];
					}
				}	
				$query_relation_types = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'catalog_types' AND sync_base='".$id."'");
				$typesRefArray =array();
				if($db->numRows($query_relation_types) > 0) 
				{					
					while($t = $db->getRow($query_relation_types))
					{	
						$typesRefArray[$t['pid']] = $t['id_1c'];
					}
				}	
				//получение ключей счетов учета
				$query_relation_chart = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'chart' AND sync_base='".$id."'");
				$chartRefArray =array();
				if($db->numRows($query_relation_chart) > 0) 
				{					
					while($u = $db->getRow($query_relation_chart))
					{	
						$chartRefArray[$u['pid']] = $u['id_1c'];
					}
				}					
				$syncDataList = array();
				$i = 0;
				$query_detail = $db->query("SELECT s.*, c.sku, c.title, c.types, c.unit, u.id as uinit_id, r.id_1c FROM ".DB_PREFIX."_stock_transfer_detail as s LEFT JOIN ".DB_PREFIX."_catalog AS c on(s.cid=c.id) LEFT JOIN ".DB_PREFIX."_catalog_unit AS u on (c.unit=u.unit) LEFT JOIN ".DB_PREFIX."_sync_relation AS r on (r.pid=s.cid AND r.module = 'catalog_v2' AND r.sync_base = '".$id."') WHERE s.transfer_id = '".$transfer['id']."' AND s.status != '0'");				
				if($db->numRows($query_detail) > 0) 
				{										
					while($d = $db->getRow($query_detail))
					{	
						$query_catalog_types = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_types WHERE id = '".$d['types']."'");				
						if($db->numRows($query_catalog_types) > 0) 
						{
							$types_data = $db->getRow($query_catalog_types);
						}
						$syncData_catalog = [
							'Артикул' => $d['sku'],					
							'НаименованиеПолное' => $d['title'],
							'Description' => $d['title'],
							'ВидНоменклатуры_Key' => $typesRefArray[$d['types']],
							'ЕдиницаИзмерения_Key' => $unitRefArray[$d['uinit_id']],
							'Услуга' => (($types_data['service'] != 1) ? 'false' : 'true'),
						];
						if(empty($d['id_1c']))
						{
							$query_catalog_check = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE pid = '".$d['cid']."' AND module = 'catalog_v2' AND sync_base = '".$id."'");				
							if($db->numRows($query_catalog_check) > 0) 
							{	
								$cr_data = $db->getRow($query_catalog_check);
								if (empty($cr_data['id_1c']))
								{	
									//echo 'ЧКлюч номенклатуры пустой: '.$d['cid'];
									$error_fix = $db->query("DELETE FROM `JMY_sync_relation` WHERE `id` = '".$cr_data['id']."' LIMIT 1");
									$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
									$sync_catalog_ref = $data_catalog->getLastId();
									$date = date('Y-m-d H:i:s');	
									$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$sync_catalog_ref."', 'catalog_v2', '".$d['cid']."', '".$id."', '".$date."', '1');");
									$cRef = $sync_catalog_ref;
								}
								else
								{
									//echo 'Ключ номенклатуры пустой: '.$d['cid'];									
									$cRef = $cr_data['id_1c'];
								}
								
							}
							else
							{
								$client->{'Catalog_Номенклатура'}->id('');
								//echo 'Ключ связи отсутствует: '.$d['cid'].'<br>';
								
								//print_r('insert');
								$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
								$sync_catalog_ref = $data_catalog->getLastId();
								$date = date('Y-m-d H:i:s');	
								$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$sync_catalog_ref."', 'catalog_v2', '".$d['cid']."', '".$id."', '".$date."', '1');");
								$cRef = $sync_catalog_ref;
							}
						}
						else
						{
							$cRef = $d['id_1c'];
						}						
						$data_check_catalog = $client->{'Catalog_Номенклатура'}->id($cRef)->get()->first();
						if(empty($data_check_catalog))
						{
							echo 'Номенклатура не найдена в базе: '.$d['cid'];							
							print_r('insert and update');
							$client->{'Catalog_Номенклатура'}->id('');
							$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData_catalog);
							$sync_catalog_ref = $data_catalog->getLastId();
							$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$d['cid']."' AND sync_base='".$id."' LIMIT 1;");							
							//exit;							
						}
						
						$i++;
						$listArray = array();
						$listArray = [
							'LineNumber' => $i,					
							'Номенклатура_Key' => $d['id_1c'],
							'КоличествоМест' => '0',
							'ЕдиницаИзмерения_Key' => $unitRefArray[$d['uinit_id']],
							'Коэффициент' => '1',
							'Количество' => $d['count'],							
							'СчетУчета_Key' => $chartRefArray['223'],
							'НовыйСчетУчета_Key' => $chartRefArray['304'],	
						];
						$syncDataList[] = $listArray;
					}					
				}
				else
				{
					$suncReturn['stock_transfer']['error']++;
					$error = 'Список товаров в перемещении пуст: '.$value['pid'];
					$suncReturn['stock_transfer']['log'][] = $error;
					$suncReturn['stock_transfer']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				$orgInfo = getOrgInfo($transfer['org']);						
				$query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$transfer['org']."' ");
				if($db->numRows($query_org) > 0) 
				{
					$r_org = $db->getRow($query_org);					
				}
				$relation_where = array();
				$relation_where[] = "(module = 'stock' AND pid = '".$transfer['stock_out']."')";
				$relation_where[] = "(module = 'stock' AND pid = '".$transfer['stock_in']."')";
				$relation_where[] = "(module = 'org' AND pid = '".$transfer['org']."')";
				$relation_where[] = "(module = 'users' AND pid = '".$transfer['uid']."')";
				$relation_where_str = implode('OR', $relation_where);
				$query_relation_stock = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE (".$relation_where_str.") AND sync_base='".$id."'");
				$refKeyArray = array();
				if($db->numRows($query_relation_stock) > 0) 
				{
					while($r = $db->getRow($query_relation_stock))
					{
						$refKeyArray[$r['module']][$r['pid']] = $r['id_1c'];	
					}					
				}
				if (isset($refKeyArray['stock'][$transfer['stock_out']])) 
				{
					$stock_out_key = $refKeyArray['stock'][$transfer['stock_out']];
				}
				else
				{
					$error = 'Не указан ключ связи для склада: '.$transfer['stock_out'];
					echo $error;
					exit;
				}
				if (isset($refKeyArray['stock'][$transfer['stock_in']])) 
				{
					$stock_in_key = $refKeyArray['stock'][$transfer['stock_in']];
				}
				else
				{
					$error = 'Не указан ключ связи для склада: '.$transfer['stock_in'];
					echo $error;
					exit;
				}
				if (isset($refKeyArray['org'][$transfer['org']])) 
				{
					$org_key = $refKeyArray['org'][$transfer['org']];
				}
				else
				{
					$error = 'Не указан ключ связи для организации: '.$transfer['org'].'/'.$transfer['id'];
					echo $error;
					exit;
				}				
				$date = explode(' ', $transfer['date']);
				$date = $date['0'].'T'.$date['1'];
				$posted = ($transfer['active'] == '1' ? 'true' : 'false');
				$comment = (!empty($transfer['comment']) ? $transfer['comment']. ' - ' : '');
				$syncData = [
					'Number' => $r_numb,
					'Date' => $date,
					'Posted' => $posted,
					'ВидОперации' => 'Товары',					
					'Организация_Key' => $org_key,
					'СкладОтправитель_Key' => $stock_out_key,
					'СкладПолучатель_Key' => $stock_in_key,									
					'Комментарий' => $comment.$object_name,					
					'Товары' => $syncDataList,
				];
				if(isset($refKeyArray['users'][$transfer['uid']]))
				{
					$syncData['Ответственный_Key'] = $refKeyArray['users'][$transfer['uid']];
				}
				$query_relation = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'stock_transfer' AND pid = '".$value['pid']."' AND sync_base='".$id."'");
				if($db->numRows($query_relation) == 1) 
				{					
					$sync_relation = $db->getRow($query_relation);	
					if (empty($sync_relation['id_1c']))
					{
						$suncReturn['stock_transfer']['error']++;	$error = 'Ключ связи пустой: '.$sync_relation['id'];
						$suncReturn['stock_transfer']['log'][] = $error;
						$suncReturn['stock_transfer']['log_detail']['error'] = $error;
						$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
						continue;
					}
					$data_check = $client->{'Document_ПеремещениеТоваров'}->id($sync_relation['id_1c'])->get()->first();
					if(!empty($data_check))
					{
						//обновление перемещения
						//print_r('update');						
						$client->{'Document_ПеремещениеТоваров'}->id('');
						$data_catalog = $client->{'Document_ПеремещениеТоваров'}->update($sync_relation['id_1c'], $syncData);						
						$suncReturn['stock_transfer']['success']++;
					}
					else
					{
						//создание перемещения и замена ключа синхронизации 
						/*
						print_r('insert and update');
						$client->{'Catalog_Номенклатура'}->id('');
						$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData);
						$sync_catalog_ref = $data_catalog->getLastId();
						$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$value['pid']."' AND sync_base='".$id."' LIMIT 1;");
						//костыль
						if($id == '1')
						{
							$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `id_1c` = '".$sync_catalog_ref."' WHERE id = '".$value['pid']."' LIMIT 1;");						
						}*/
						print_r('создание перемещения и замена ключа синхронизации перемещение'.$sync_relation['id']);
						exit;
					}			
				}
				elseif($db->numRows($query_relation) > 1) 
				{
					$suncReturn['stock_transfer']['error']++;
					$error = 'Найден дубликат ключей синхронизации номенклатуры. Идентификатор номенклатуры: '.$catalog['id'];
					$suncReturn['stock_transfer']['log'][] = $error;
					$suncReturn['stock_transfer']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				else
				{
					//создание перемещения				
					$client->{'Document_ПеремещениеТоваров'}->id('');
					//print_r('insert');
					$data_catalog = $client->{'Document_ПеремещениеТоваров'}->create($syncData);
					$sync_catalog_ref = $data_catalog->getLastId();
					//dd($data_catalog);
					//dd($syncData);
					//exit;
					$date = date('Y-m-d H:i:s');	
					syncRelation($transfer['id'], 'stock_transfer', $sync_catalog_ref, $id);					
					//print_r($syncData);
					$suncReturn['stock_transfer']['success']++;
					
					
				}
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '2', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$count_send++;	
				$suncReturn['stock_transfer']['numb']++;
			}
			else
			{
		
				$suncReturn['stock_transfer']['error']++;
				$error = 'Не найдено перемещение: '.$value['pid'];
				$suncReturn['stock_transfer']['log'][] = $error;
				$suncReturn['stock_transfer']['log_detail']['error'] = $error;
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				continue;
			}
		
		}
	}
