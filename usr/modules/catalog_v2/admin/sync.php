<?php
	function syncUnitRelation($unit_id, $ref_key, $sync_base)
	{
		global $core, $db;
		$query_relation_unit = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'unit' AND pid = '".$unit_id."' AND sync_base='".$sync_base."'");
		if($db->numRows($query_relation_unit) <= 0) 
		{
			$date = date('Y-m-d H:i:s');	
			$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$ref_key."', 'unit', '".$unit_id."', '".$sync_base."', '".$date."', '1');");
			$unit_code = $db->insert_id();
			return $unit_code['id_1c'];
		}
		elseif($db->numRows($query_relation_unit) > 1)
		{
			$error = 'Найден дубликат ключей синхронизации единиц измерения. Идентификатор единицы измерения: '.$unit_id;
			echo $error;
			exit;
		}
		else
		{
			$unit_relation = $db->getRow($query_relation_unit);	
			return $unit_relation['id_1c'];
		}
	}
	
	function syncTypesRelation($types_id, $ref_key, $sync_base)
	{
		global $core, $db;
		$query_relation_types = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'catalog_types' AND pid = '".$types_id."' AND sync_base='".$sync_base."'");
		if($db->numRows($query_relation_types) <= 0) 
		{
			$date = date('Y-m-d H:i:s');	
			$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$ref_key."', 'catalog_types', '".$types_id."', '".$sync_base."', '".$date."', '1');");
			$types_code = $db->insert_id();
			return $types_code['id_1c'];
		}
		elseif($db->numRows($query_relation_types) > 1)
		{
			$error = 'Найден дубликат ключей синхронизации видов номенклатуры. Идентификатор вида номенклатуры: '.$types_id;
			echo $error;
			exit;
		}
		else
		{
			$types_relation = $db->getRow($query_relation_types);	
			return $types_relation['id_1c'];
		}
	}
	
	
	if(isset($syncArray['catalog_v2']))
	{
		$uid = $core->auth->user_info['id'];
		//Обработчик видов номенклатуры
		$catalog_type_get = $client->{'Catalog_ВидыНоменклатуры'}->get()->values();
		foreach ($catalog_type_get as $key => $value)
		{
			$query_types = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_types WHERE title = '".$value['Description']."'");
			if($db->numRows($query_types) > 0) 
			{
				$types = $db->getRow($query_types);	
				syncTypesRelation($types['id'], $value['Ref_Key'], $id);
			}
			else
			{
				$service = '0';
				if ($value['Услуга'] == 1)
				{
					$service = '1';
				}
				$date = date('Y-m-d H:i:s');	
				$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_types` ( `id` , `uid`, `title`, `service`,  `date`, `status`) VALUES (NULL, '".$uid."', '".$value['Description']."', '".$service."', '".$date."', '1');");					
				$types = $db->insert_id();
				syncTypesRelation($types, $value['Ref_Key'], $id);					
			}			
		}		
		//Обработчик единиц измерения
		$unit_get = $client->{'Catalog_КлассификаторЕдиницИзмерения'}->get()->values();
		//echo '<pre>';
		
		foreach ($unit_get as $key => $value)
		{
			if (!empty(intval($value['Code'])))
			{
				$query_unit = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_unit WHERE unit = '".$value['Code']."'");
				if($db->numRows($query_unit) > 0) 
				{
					$unit = $db->getRow($query_unit);	
					syncUnitRelation($unit['id'], $value['Ref_Key'], $id);
				}
				else
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_catalog_unit` ( `id` , `unit`, `name`,  `sname`) VALUES (NULL, '".$value['Code']."', '".$value['НаименованиеПолное']."', '".$value['Description']."');");					
					$unit = $db->insert_id();
					syncUnitRelation($unit, $value['Ref_Key'], $id);					
				}
			}
		}
		//Обработчик товаров
		foreach ($syncArray['catalog_v2'] as $key => $value)
		{
			$query_catalog = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '".$value['pid']."'");
            if($db->numRows($query_catalog) > 0) 
            {					
                $catalog = $db->getRow($query_catalog);	
				$unit_code = '';
				//echo $catalog['unit'];
				//print_r($unit_get);
				foreach ($unit_get as $ukey => $uvalue)
				{
					//echo $uvalue['Code'].'<br>';
					if(trim($uvalue['Code']) == $catalog['unit'])
					{
						$unit_code = $uvalue['Ref_Key'];
						//echo 'ffff';
					}
				}
				//echo $unit_code;
				if(empty($unit_code))
				{
					$query_catalog_unit = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_unit WHERE unit = '".$catalog['unit']."'");
					if($db->numRows($query_catalog_unit) > 0) 
					{
						$unit_data = $db->getRow($query_catalog_unit);
						$sync_unit = $client->{'Catalog_КлассификаторЕдиницИзмерения'}->create([
							'Code'=> $unit_data['unit'],
							'Description'=> $unit_data['sname'],
							'НаименованиеПолное'=> $unit_data['name'],
						]);
						$sync_unit_ref = $sync_unit->getLastId();
						$unit_code = syncUnitRelation($unit_data['unit'], $sync_unit_ref, $id);						
					}
					else
					{
						$error = 'Нет соответствия к данной единице измерения: '.$catalog['unit'];
						echo $error;
						exit;
					}
				}				
				$query_catalog_types = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_types WHERE id = '".$catalog['types']."'");				
				if($db->numRows($query_catalog_types) > 0) 
				{
					$types_data = $db->getRow($query_catalog_types);
					$types_code = '';
					foreach ($catalog_type_get as $tkey => $tvalue)
					{
						if($tvalue['Description'] == $types_data['title'])
						{
							$types_code = $tvalue['Ref_Key'];
						}
					}
					if(empty($types_code))
					{
						$sync_types = $client->{'Catalog_ВидыНоменклатуры'}->create([
							'Description'=> $types_data['title'],
							'Услуга'=>  (($types_data['service'] != 1) ? '' : '1'),
						]);
						$sync_types_ref = $sync_types->getLastId();
						$types_code = syncTypesRelation($catalog['types'], $sync_types_ref, $id);						
					}					
				}
				else
				{
					$error = 'Нет соответствия к данному виду номенклатуры: '.$catalog['types']. $catalog['title'];
					echo $error;
					exit;
				}
				$syncData = [
					'Артикул' => $catalog['sku'],					
					'НаименованиеПолное' => addslashes($catalog['title']),
					'Description' => addslashes($catalog['title']),
					'ВидНоменклатуры_Key' => $types_code,
					'ЕдиницаИзмерения_Key' => $unit_code,
					'Услуга' => (($types_data['service'] != 1) ? 'false' : 'true'),
				];
				$query_relation = $db->query("SELECT * FROM ".DB_PREFIX."_sync_relation WHERE module = 'catalog_v2' AND pid = '".$value['pid']."' AND sync_base='".$id."'");
				if($db->numRows($query_relation) == 1) 
				{
					$sync_relation = $db->getRow($query_relation);	
					if (empty($sync_relation['id_1c']))
					{
						
						$suncReturn['catalog_v2']['error']++;
						$error = 'Ключ связи пустой: '.$sync_relation['id'];
						$suncReturn['catalog_v2']['log'][] = $error;
						$suncReturn['catalog_v2']['log_detail']['error'] = $error;
						continue;
					}
					$data_check = $client->{'Catalog_Номенклатура'}->id($sync_relation['id_1c'])->get()->first();
					if(!empty($data_check))
					{
						//обновление номенклатуры					
						$client->{'Catalog_Номенклатура'}->id('');
						$data_catalog = $client->{'Catalog_Номенклатура'}->update($sync_relation['id_1c'],$syncData);						
						$suncReturn['catalog_v2']['success']++;
						//$suncReturn['catalog_v2']['log'][] = 'update->'.json_encode($syncData, JSON_UNESCAPED_UNICODE);
						//$suncReturn['catalog_v2']['log_detail']['update'] = json_encode($syncData, JSON_UNESCAPED_UNICODE);
					}
					else
					{
						//создание номенклатуры и замена ключа синхронизации 						
						$client->{'Catalog_Номенклатура'}->id('');
						$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData);
						$sync_catalog_ref = $data_catalog->getLastId();
						$db->query("UPDATE `" . DB_PREFIX . "_sync_relation` SET `id_1c` = '".$sync_catalog_ref."' WHERE module = 'catalog_v2' AND pid = '".$value['pid']."' AND sync_base='".$id."' LIMIT 1;");
						//костыль
						if($id == '1')
						{
							$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `id_1c` = '".$sync_catalog_ref."' WHERE id = '".$value['pid']."' LIMIT 1;");						
						}
						$suncReturn['catalog_v2']['success']++;
						//$suncReturn['catalog_v2']['log'][] = 'insert and update->'.json_encode($syncData, JSON_UNESCAPED_UNICODE);
						//$suncReturn['catalog_v2']['log_detail']['insert_update'] = json_encode($syncData, JSON_UNESCAPED_UNICODE);
					}				
				}
				elseif($db->numRows($query_relation) > 1) 
				{
					$suncReturn['catalog_v2']['error']++;
					$error = 'Найден дубликат ключей синхронизации номенклатуры. Идентификатор номенклатуры: '.$catalog['id'];
					$suncReturn['catalog_v2']['log'][] = $error;
					$suncReturn['catalog_v2']['log_detail']['error'] = $error;
					$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
					continue;
				}
				else
				{
					//создание номенклатуры
					$client->{'Catalog_Номенклатура'}->id('');
					$data_catalog = $client->{'Catalog_Номенклатура'}->create($syncData);
					//echo '<pre>';
					//print_r($syncData);
					//print_r($data_catalog);
					//exit;
					$sync_catalog_ref = $data_catalog->getLastId();
					$date = date('Y-m-d H:i:s');	
					$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c`, `module`,  `pid`, `sync_base`, `date`, `status`)  VALUES (NULL, '".$sync_catalog_ref."', 'catalog_v2', '".$value['pid']."', '".$id."', '".$date."', '1');");
					//костыль
					if($id == '1')
					{
						$db->query("UPDATE `" . DB_PREFIX . "_catalog` SET `id_1c` = '".$sync_catalog_ref."' WHERE id = '".$value['pid']."' LIMIT 1;");						
					}
					$suncReturn['catalog_v2']['success']++;
					//$suncReturn['catalog_v2']['log'][] = 'insert->'.json_encode($syncData, JSON_UNESCAPED_UNICODE);
					//$suncReturn['catalog_v2']['log_detail']['insert'] = json_encode($syncData, JSON_UNESCAPED_UNICODE);
				}
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '2', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$count_send++;
				$suncReturn['catalog_v2']['numb']++;
			}
			else
			{
				$db->query("UPDATE `" . DB_PREFIX . "_sync_register` SET `status` = '3', `sync_id` = '".$sync_id."', `date_sync` = '".date('Y-m-d H:i:s')."' WHERE id = '".$value['id']."' LIMIT 1;");
				$suncReturn['catalog_v2']['numb']++;
				$suncReturn['catalog_v2']['error']++;
				$error = 'Номенклатура не найдена: '.$value['pid'];
				$suncReturn['catalog_v2']['log'][] = $error;
				$suncReturn['catalog_v2']['log_detail']['error'] = $error;
				
				//$suncReturn['catalog_v2']['log'][] = $error;
			}
			
		}
	}
