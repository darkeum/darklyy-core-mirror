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

global $core, $db;

function main($form = null, $name = null) 
{
	global $core, $db, $url;
	$core->tpl->breadcrumbs[0] = array('name' => 'Главная', 'url' => '/', 'active' => '0');
	$core->tpl->breadcrumbs[1] = array('name' => 'Для проектировщика', 'url' => '/content/tool.html', 'active' => '0');
	$core->tpl->breadcrumbs[2] = array('name' => $name, 'url' => '/form/'.$url[1], 'active' => '1');
	$breadcrumbs = $core->tpl->get_breadcrumbs();
	set_title(array($name));
	$regionArray = getRegionArray();
	$region_select = '';
	foreach ($regionArray as $key => $value) 
	{
		$region_select .= '<option value="'.$key.'">'.$value.'</option>';
	}
	 $city_select = '';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_geo_city ORDER BY title ASC");	
			if($db->numRows($query) > 0)
			{
				
                while($object = $db->getRow($query))
                {
                    $city_select .= '<option value="'.$object['id'].'" >'.$object['title'].'</option>';			
                }
			}
			 $tso_select = '';
		$query_tso = $db->query("SELECT * FROM ".DB_PREFIX."_geo_tso ORDER BY title ASC");	
			if($db->numRows($query_tso) > 0)
			{
				
                while($object_tso = $db->getRow($query_tso))
                {
                    $tso_select .= '<option value="'.$object_tso['id'].'" >'.$object_tso['title'].'</option>';			
                }
			}	
			$attribute["script"] = true;
										$attribute["name"] = 'sm';//добавочное имя
										/*
											$attribute["file"] - файл
											$attribute["module"] - имя модуля
											$attribute["mod"] - имя мода (task, task_comment...)
											$attribute["id"] - id объекта
											$attribute["comm_id"] - id комментария
										*/
										$upload =  uploaderMaster('upload', $attribute);
										$view = uploaderMaster('view', $attribute);
										echo uploaderMaster('plugin', $attribute);
		echo uploaderMaster('script', $attribute);
		echo uploaderMaster('css', $attribute);
	$core->tpl->loadFile('form/form.'.$form);	
	$core->tpl->setVar('BREADCRUMBS', $breadcrumbs);	
	$core->tpl->setVar('NAME', $name);		
	$core->tpl->setVar('REGION_SELECT', $region_select);		
	$core->tpl->setVar('CITY_SELECT', $city_select);		
	$core->tpl->setVar('TSO_SELECT', $tso_select);		
	$core->tpl->setVar('UPLOAD_INIT', $upload);		
	$core->tpl->setVar('UPLOAD_VIEW', $view);		
	
	$core->tpl->setVar('CAPTCHA', captcha_image());
	$core->tpl->end();	
}

										
switch(isset($url[1]) ? $url[1] : null) 
{
	default:	
		errorLoad();
	break;
	
	case "pump": 		
		main('pump', 'Подбор насосного оборудования');
	break;
	
	case "heat_exchanger":
		header('Location: /project/calculation/heat');
exit;
	break;
	
	case "metering_station":
		main('metering_station', 'Подбор узла учета');
	break;
	
	case "pump_station":
		main('pump_station', 'Подбор насосной станции');
	break;
	
	case "send":
		switch(isset($url[2]) ? $url[2] : null) 
		{
			default:	
				errorLoad();
			break;
			
			case "pump":		
				set_title(array($lang['feedback'], 'Отправка сообщения'));
				if(captcha_check('securityCode')) 
				{
					$topic = 'Заявка на подбор насосного оборудования';
					$email = isset($_POST['email']) ? filter($_POST['email']) : '';
					$name = isset($_POST['name']) ? filter($_POST['name']) : '';
					$org = isset($_POST['org']) ? filter($_POST['org']) : '';
					$inn = isset($_POST['inn']) ? filter($_POST['inn']) : '';
					$phone = isset($_POST['phone']) ? filter($_POST['phone']) : '';
					$message = isset($_POST['message']) ? filter($_POST['message'], 'html') : '';
					$object = isset($_POST['object']) ? filter($_POST['object']) : '';
					$syn = isset($_POST['r1']) ? filter($_POST['r1']) : '';
					$rashod = isset($_POST['rashod']) ? filter($_POST['rashod']) : '';
					$napor = isset($_POST['napor']) ? filter($_POST['napor']) : '';
					$pch = isset($_POST['r2']) ? filter($_POST['r2']) : '';
					if(!empty($topic) && !empty($phone)) 
					{	
						$db->query("INSERT INTO `" . DB_PREFIX . "_object` ( `id` , `name`, `status`)  VALUES (NULL, '".$object."', '3');");
						$object_id = $db->insert_id();	
						$oid = createOrgByInn($inn);						
						if ($core->auth->isUser)
						{
							$uid = $core->auth->user_info['id'];
						}
						else
						{
							$tail = gencode(rand(6, 11));
							$password = gencode(rand(6, 11));
							$core->auth->register($email, $password, $tail, $email, '', '', '', $name, $ochestvo, '', '', '', '', '3', '', '', $phone);
							$uid = $db->insert_id();	
						}
						$cat = 1;
						$date = time();						
						if($db->query("INSERT INTO `" . DB_PREFIX . "_form` ( `id` , `uid` , `cat` , `oid` ,  `object` ,  `date`, `status`) VALUES (NULL, '" . $uid . "', '" . $cat . "', '" . $oid . "', '" . $object_id . "', '" . $date . "','1');"))							
						{
							$fid = $db->insert_id();
							$db->query("INSERT INTO `" . DB_PREFIX . "_form_pump` ( `id` , `fid` , `syn` , `rashod` , `napor` , `pch` , `comment`) VALUES (NULL, '" . $fid . "', '" . $syn . "', '" . $rashod . "', '" . $napor . "', '" . $pch . "', '" . $message . "');");
							$text = 'Новая заявка на расчёт насосного оборудования от '.$org;
							sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('1'));
							sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('4'));
							//sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('1'));
						}							
						$core->tpl->info('Заявка на расчет насосного оборудования успешно отправлена!');
					} 
					else 
					{
						$core->tpl->info('Произошла ошибка при отправке сообщения!', 'warning');
					}
				} 
				else 
				{
					$core->tpl->info('Вы не прошли проверку на робота!', 'warning');
				}
			break;
			case "pump_station":		
				set_title(array($lang['feedback'], 'Отправка сообщения'));
				if(captcha_check('securityCode')) 
				{
					$topic = 'Заявка на подбор насосной станции';
					$email = isset($_POST['email']) ? filter($_POST['email']) : '';
					$name = isset($_POST['name']) ? filter($_POST['name']) : '';
					$org = isset($_POST['org']) ? filter($_POST['org']) : '';
					$inn = isset($_POST['inn']) ? filter($_POST['inn']) : '';
					$phone = isset($_POST['phone']) ? filter($_POST['phone']) : '';
					$message = isset($_POST['message']) ? filter($_POST['message'], 'html') : '';
					$object = isset($_POST['object']) ? filter($_POST['object']) : '';
					$syn = isset($_POST['r1']) ? filter($_POST['r1']) : '';
					$rashod = isset($_POST['rashod']) ? filter($_POST['rashod']) : '';
					$napor = isset($_POST['napor']) ? filter($_POST['napor']) : '';
					$sum = isset($_POST['sum']) ? filter($_POST['sum']) : '';
					$sumr = isset($_POST['sumr']) ? filter($_POST['sumr']) : '';
					$tocha = isset($_POST['tocha']) ? filter($_POST['tocha']) : '';
					$pch = isset($_POST['r2']) ? filter($_POST['r2']) : '';
					if(!empty($topic) && !empty($phone)) 
					{	
						$db->query("INSERT INTO `" . DB_PREFIX . "_object` ( `id` , `name`, `status`)  VALUES (NULL, '".$object."', '3');");
						$object_id = $db->insert_id();	
						$oid = createOrgByInn($inn);						
						if ($core->auth->isUser)
						{
							$uid = $core->auth->user_info['id'];
						}
						else
						{
							$queryO = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE email = '" . $email . "' LIMIT 1");		
							if($db->numRows($queryO) <= 0)
							{
								$tail = gencode(rand(6, 11));
								$password = gencode(rand(6, 11));
								$core->auth->register($email, $password, $tail, $email, '', '', '', $name, $ochestvo, '', '', '', '', '3', '', '', $phone);
								$uid = $db->insert_id();	
							}
							else
							{
								$resultO = $db->getRow($queryO);
								$uid = $resultO['id'];
							}
						}
						$cat = 2;
						$date = time();						
						if($db->query("INSERT INTO `" . DB_PREFIX . "_form` ( `id` , `uid` , `cat` , `oid` ,  `object` ,  `date`, `status`) VALUES (NULL, '" . $uid . "', '" . $cat . "', '" . $oid . "', '" . $object_id . "', '" . $date . "','1');"))							
						{
							$fid = $db->insert_id();
							$db->query("INSERT INTO `" . DB_PREFIX . "_form_pump_station` ( `id` , `fid` , `syn` , `sum` , `sumr` , `tocha` , `rashod` , `napor` , `pch` , `comment`) VALUES (NULL, '" . $fid . "', '" . $syn . "', '" . $sum . "', '" . $sumr . "', '" . $tocha . "', '" . $rashod . "', '" . $napor . "', '" . $pch . "', '" . $message . "');");
							$text = 'Новая заявка на расчёт насосной станции от '.$org;
							sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('1'));
							sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('4'));
							sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('9'));
							//sendPush('Новая заявка на расчёт!', $text, config('app.url').'/files/push/task.png', config('app.url').'/administration/task', getClientId('1'));
						}							
						$core->tpl->info('Заявка на расчет насосной станции успешно отправлена!');
					} 
					else 
					{
						$core->tpl->info('Произошла ошибка при отправке сообщения!', 'warning');
					}
				} 
				else 
				{
					$core->tpl->info('Вы не прошли проверку на робота!', 'warning');
				}
			break;
		}
	break;		
}