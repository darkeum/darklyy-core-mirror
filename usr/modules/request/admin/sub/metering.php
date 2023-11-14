<?php

global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;

class metering {

    var $id; //Идентификатор расчета
    var $uid; //Идентификатор текущего пользователя
    var $dir; //Директория для работы с файлами
	var $sprav = array(); //Массив со справочной информацией
	var $project_info = array(); //Массив с информацией о расчете
	var $project_quipment = array(); //Массив с информацией о оборудовании
	var $project_system = array(); //Массив с информацией о подключенных системах
	var $project_parameter = array(); //Массив с информацией о входных и выходных параметрах
	var $project_dn = array(); //Информация о диаметрах труб
	var $scheme; //Номер схемы (см. на странице расчета)
	var $region; //Регион 
	var $city; //Город 
	var $tso; //ТСО
	var $result = array(); //Массив с итоговыми значениями
	
	//инициализация класса получение начальных данных
    function __construct($id)
    {
		global $core, $db;	
		$this->id = intval($id);
		$this->uid = $core->auth->user_info['id'];
		$dir = ROOT.'files/metering/output';
		if (!file_exists($dir))
		{
			mkdir($dir, 0777);						
		}
		$this->dir = $dir;		
	}
	
	public function init()
	{
		$this->getSpravData();	
		$this->getMainData();	
		$this->getDn();
		$this->getResult();
		
	}
	
	public function showParametr($parament)
	{
		print_r('<h1>'.$parament.'</h1>');
		print_r('<pre>');	
		print_r($this->$parament);	
		print_r('<br>');	
	}
	
	public function showAllParametr()
	{
		$this->showParametr('id');	
		$this->showParametr('uid');	
		$this->showParametr('dir');	
		$this->showParametr('sprav');	
		$this->showParametr('project_info');	
		$this->showParametr('project_quipment');	
		$this->showParametr('project_system');	
		$this->showParametr('project_parameter');	
		$this->showParametr('project_dn');	
		$this->showParametr('scheme');	
		$this->showParametr('result');	
	}
	
	//замена запятых на точку
	private function commasReplace($text)
	{
		return str_replace(',','.',$text);	
	}
	
	//замена точек на запятые
	private function dotsReplace($text)
	{
		return str_replace('.',',',$text);	
	}
	
	//получение название схемы
	public function getSchemeName($scheme = false)
	{
		if($scheme == false)
		{
			$scheme_id = $this->scheme;
		}
		else
		{
			$scheme_id = $scheme;
		}
		switch($scheme_id) 
		{
			default:
				$return = 'Неизвестная схема';
			break;
			case "1":
				$return = 'Схема №1 (2-х трубная)';
			break;
			case "2":
				$return = 'Схема №2 (2-х трубная с подпиткой)';
			break;
			case "3":
				$return = 'Схема №3 (3-х трубная с ГВС)';
			break;
			case "4":
				$return = 'Схема №4 (4-х трубная с ГВС и циркуляцией)';
			break;
		}
		return $return;
	}
	
	//заполнение табличных данных
	public function getSpravData()
	{
		//возможные внутренние диаметры 
		$this->sprav['dn'] = array(15,20,25,32,40,50,65,80,100,125,150,200,250,300);
		//наружные диаметры в соответствии с внутренними диаметрами
		$this->sprav['dn_n'] = array(
			'15' => '21.3',
			'20' => '25',
			'25' => '32',
			'32' => '38',
			'40' => '45',
			'50' => '57',
			'65' => '76',
			'80' => '89',
			'100' => '108',
			'125' => '133',
			'150' => '159',
			'200' => '219',
			'250' => '273',
			'300' => '325'
		);
		//толщина трубы в соответствии с внутренними диаметрами
		$this->sprav['tolshina'] =  array(	
			'15' => '2.8',			
			'20' => '2.8',
			'25' => '3.2',
			'32' => '3.2',
			'40' => '3.5',
			'50' => '3.5',
			'65' => '3.5',
			'80' => '4',
			'100' => '4',
			'125' => '4.5',
			'150' => '4.5',
			'200' => '6',
			'250' => '7',
			'300' => '8'
		);
		//толщина бесшовной трубы в соответствии с внутренними диаметрами
		$this->sprav['tolshina_bezshov'] =  array(	
			'15' => '2.5',								
			'20' => '2.5',
			'25' => '3',
			'32' => '3',
			'40' => '3.5',
			'50' => '3.5',
			'65' => '3.5',
			'80' => '5',
			'100' => '4',
			'125' => '5',
			'150' => '6',
			'200' => '6',
			'250' => '7',
			'300' => '8'
		);
		
		
		//сопоставление ТСО и температур по СНИП'ам 
		$this->sprav['t_snip_by_tso'] =  array(	
			'2' => '1',		
		);
		//Температуры по СНИП'ам помесячно
		$this->sprav['t_snip']['1'] =  array(	
			'1'  => array('day_count' => '31', 't_sr' => '-6.6', 't_min' => '-24', 't_room' => '20'),
			'2'  => array('day_count' => '28', 't_sr' => '-6.3', 't_min' => '-24', 't_room' => '20'),
			'3'  => array('day_count' => '31', 't_sr' => '-1.5', 't_min' => '-24', 't_room' => '20'),
			'4'  => array('day_count' => '30', 't_sr' => '4.5', 't_min' => '-24', 't_room' => '20'),
			'5'  => array('day_count' => '1', 't_sr' => '10.9', 't_min' => '-24', 't_room' => '20'),
			'6'  => array('day_count' => '0', 't_sr' => '0', 't_min' => '-24', 't_room' => '20'),
			'7'  => array('day_count' => '0', 't_sr' => '0', 't_min' => '-24', 't_room' => '20'),
			'8'  => array('day_count' => '0', 't_sr' => '0', 't_min' => '-24', 't_room' => '20'),
			'9'  => array('day_count' => '0', 't_sr' => '0', 't_min' => '-24', 't_room' => '20'),
			'10' => array('day_count' => '31', 't_sr' => '5.7', 't_min' => '-24', 't_room' => '20'),
			'11' => array('day_count' => '30', 't_sr' => '0.2', 't_min' => '-24', 't_room' => '20'),
			'12' => array('day_count' => '31', 't_sr' => '-3.9', 't_min' => '-24', 't_room' => '20'),			
		);
		
	}
	
	//получение основных данных о расчете из базы данных
	public function getMainData()
	{
		global $core, $db;	
		$query = $db->query("SELECT r.*, o.name as o_name, obj.name as object_name, obj.address as object_address FROM " . DB_PREFIX . "_request as r LEFT JOIN " . DB_PREFIX . "_object as obj ON(r.object = obj.id) LEFT JOIN " . DB_PREFIX . "_organizations as o ON(r.oid = o.id) WHERE r.id = '" . $this->id . "' LIMIT 1");
		$request = $db->getRow($query);	
		$requestData = json_decode($request['request_data'], true);	
		//получение номера схемы
		$this->scheme = (!empty($requestData['shema']) ? $requestData['shema'] : '1');
		//получение региона города и ТСО
		$this->region = (!empty($requestData['region']) ? $requestData['region'] : '77');
		$this->city = (!empty($requestData['city']) ? $requestData['city'] : '1');
		$this->tso = (!empty($requestData['tso']) ? $requestData['tso'] : '1');
		//получение номера проекта
		$this->project_info['pr_numb'] = (!empty($requestData['number'])  ? $requestData['number'] : 'XXXXXX');
		//получение разработчика проекта, проверяющего, ГИП'а и контролера
		$this->project_info['raz'] = (!empty($requestData['raz'])  ? $requestData['raz'] : '');
		$this->project_info['prov'] = (!empty($requestData['prov'])  ? $requestData['prov'] : '');
		$this->project_info['gip'] = (!empty($requestData['gip'])  ? $requestData['gip'] : '');
		$this->project_info['control'] = (!empty($requestData['control'])  ? $requestData['control'] : '');
		//получение номера и даты тех. условий
		$this->project_info['tu_numb'] = (!empty($requestData['tuNumber'])  ? $requestData['tuNumber'] : '');
		$this->project_info['tu_date'] = (!empty($requestData['tuData'])  ? $requestData['tuData'] : '');
		//получение номера и даты договора теплоснабжения
		$this->project_info['dog_numb'] = (!empty($requestData['dogNumber'])  ? $requestData['dogNumber'] : '');
		$this->project_info['dog_date'] = (!empty($requestData['dogData'])  ? $requestData['dogData'] : '');
		//получение организации названия и адрес объекта
		$this->project_info['org_name'] = (!empty($request['o_name'])  ? $request['o_name'] : 'ООО «АТРИ-ЭНЕРГО»');
		$this->project_info['object_name'] = (!empty($request['object_name'])  ? $request['object_name'] : 'Фруктовый сад');
		$this->project_info['object_address'] = (!empty($request['object_address'])  ? $request['object_address'] : 'г. Ижевск, ул. Салютовская, д. 5а');
		//получение даты проекта
		$this->project_info['date'] = date('m.y');
		//заполнение идентификаторов производителей оборудования
		$this->project_quipment['kran']['mid'] = '4'; //кран		
		$this->project_quipment['kran_muf']['mid'] = '6'; //кран на 3 трубопровод муфтовый 
		$this->project_quipment['zatvor']['mid'] = '13'; //затвор на 3 трубопровод		
		$this->project_quipment['kran_sp']['mid'] = '6'; //кран под спусник
		$this->project_quipment['manometr']['mid'] = '18'; //манометр
		$this->project_quipment['kran_manometr']['mid'] = '7'; //кран под манометр
		$this->project_quipment['termometr']['mid'] = '1'; //термометр
		$this->project_quipment['gilza']['mid'] = '1'; //гильза под термометр
		$this->project_quipment['bob']['mid'] = '1'; //бобышка
		$this->project_quipment['rezba']['mid'] = '1'; //резьба
		$this->project_quipment['otvod']['mid'] = '1'; //отвод
		$this->project_quipment['perkensa']['mid'] = '1'; //трубка Перкинса
		//заполнение идентификаторов производителей основного оборудования
		//заполнение термопреобразователя 
		switch($requestData['preobTeplo']) 
		{       
			case "1":			
				$this->project_quipment['preobTeplo']['title'] = 'КТПТР-01';
				$this->project_quipment['preobTeplo']['man'] = 'ЗАО «ТЕРМИКО»';
				$this->project_quipment['preobTeplo']['mid'] = '8';	
				//минимальная разность температур для таблицы программирования
				$this->project_quipment['preobTeplo']['dtmin']	= '2';
				//дельта T
				$this->project_quipment['preobTeplo']['deltat']	= '0...180';
				//класс допуска
				$this->project_quipment['preobTeplo']['klass']['1']	= 'АА';
				$this->project_quipment['preobTeplo']['klass']['2']	= '1';
				
			break;
			case "2":
				$this->project_quipment['preobTeplo']['title'] = 'ТСП-К-101';
				$this->project_quipment['preobTeplo']['man'] = 'НПП «ПРОМА»';
				$this->project_quipment['preobTeplo']['mid'] = '40';
				//минимальная разность температур для таблицы программирования
				$this->project_quipment['preobTeplo']['dtmin']	= '3';
				//дельта T
				$this->project_quipment['preobTeplo']['deltat']	= '3...160';
				//класс допуска
				$this->project_quipment['preobTeplo']['klass']['1']	= 'А';
				$this->project_quipment['preobTeplo']['klass']['2']	= 'А';
			break;		
		}
		$this->project_quipment['preobTeplo']['select']	= $requestData['preobTeplo'];	
		//заполнение проборазователя давления 
		switch($requestData['preobDav']) 
		{       
			case "1":			
				$this->project_quipment['preobDav']['title'] = 'СДВ-И';
				$this->project_quipment['preobDav']['man'] = 'НПК «ВИП»';
				$this->project_quipment['preobDav']['mid'] = '25';
				$this->project_quipment['preobDav']['predel'] = 'Однопредельный';
			break;
			case "2":
				$this->project_quipment['preobDav']['title'] = 'ДДМ 03Т';
				$this->project_quipment['preobDav']['man'] = 'НПП «ПРОМА»';
				$this->project_quipment['preobDav']['mid'] = '40';
				$this->project_quipment['preobDav']['predel'] = 'Однопредельный';
			break;		
			case "3":
				$this->project_quipment['preobDav']['title'] = 'ПДТВХ-1-02';
				$this->project_quipment['preobDav']['man'] = 'ООО НПП «ТЕПЛОВОДОХРАН»';
				$this->project_quipment['preobDav']['mid'] = '19';
				$this->project_quipment['preobDav']['predel'] = 'Однопредельный';
			break;	
		}	
		$this->project_quipment['preobDav']['select']	= $requestData['preobDav'];	
		//заполнение данных о расходомере 
		$teplo_manufacture = 1;
		switch($teplo_manufacture) 
		{       
			case "1":			
				$this->project_quipment['preborRashod']['mid'] = '11';
				$this->project_quipment['preborRashod']['man'] = 'ООО «ТЕРМОТРОНИК»';
				$this->project_quipment['preborRashod']['cat'] = '470';
				$this->project_quipment['preborRashod']['select'] = '1';
				//идентификатор подходящего тепловычислителя
				$this->project_quipment['preborRashod']['v_id']['1'] = '38251'; 				
				$this->project_quipment['preborRashod']['v_id']['2'] = '38249'; 				
				$this->project_quipment['preborRashod']['v_id']['3'] = '38249'; 				
				$this->project_quipment['preborRashod']['v_id']['4'] = '38249'; 				
			break;			
		}
		//заполнение входных и выходных параметров параметров		
		$this->project_parameter['t1'] = (isset($requestData['t1']) ? $this->commasReplace($requestData['t1']) : '0');
		$this->project_parameter['t2'] = (isset($requestData['t2']) ? $this->commasReplace($requestData['t2']) : '0');
		$this->project_parameter['t3'] = (isset($requestData['t3']) ? $this->commasReplace($requestData['t3']) : '0');
		$this->project_parameter['t4'] = (isset($requestData['t4']) ? $this->commasReplace($requestData['t4']) : '0');
		$this->project_parameter['p1'] = (isset($requestData['p1']) ? $this->commasReplace($requestData['p1']) : '0');
		$this->project_parameter['p2'] = (isset($requestData['p2']) ? $this->commasReplace($requestData['p2']) : '0');
		$this->project_parameter['p3'] = (isset($requestData['p3']) ? $this->commasReplace($requestData['p3']) : '0');
		$this->project_parameter['p4'] = (isset($requestData['p4']) ? $this->commasReplace($requestData['p4']) : '0');
		//получение информации о подключенных системах
		$inputDataJSON = $requestData['inputDataJSON'];		
		$g_all = 0;
		$i = 0;	
		$g_min_sum = 0;
		$g_max_sum = 0;
		$sum_podpitka = 0;
		$vent_only = true;
		$sys_title_array = array();
		$this->project_system['gvs']['1']['q'] = '0';
		$this->project_system['gvs']['1']['g'] = '0';
		$this->project_system['gvs']['1']['short'] = '0';
		$this->project_system['gvs']['1']['title'] = '0';
		$this->project_system['gvs']['1']['type'] = '0';
		foreach ($inputDataJSON['data'] as $key => $value) 
		{
			$i++;
			$qq = $this->commasReplace($value['q']);
			$this->project_system['system'][$i]['type'] = $value['name'];		
			$this->project_system['system'][$i]['q'] = $qq;			
			switch($value['name']) 
			{  
				//система ГВС			
				case "gvs":
					if($this->scheme == '1' || $this->scheme == '2')
					{	
						if($value['sel'] == 'Параллельная')
						{
							$this->project_system['system'][$i]['g'] = round($qq*1000/40, 3);					
						}
						else
						{
							$this->project_system['system'][$i]['g'] = round($qq*1200/60, 3);
						}
						$this->project_system['system'][$i]['sort'] = '3';
						$sys_title_array['gvs'] = 'ГВС';
						$vent_only = false;
						$this->project_system['gvs'][$i]['q'] = $qq;
						$this->project_system['gvs'][$i]['g'] = $this->project_system['system'][$i]['g'];
						$this->project_system['gvs'][$i]['sort'] = '3';
						$this->project_system['gvs'][$i]['title'] = $sys_title_array['gvs'];
						$this->project_system['gvs'][$i]['type'] = $value['name'];						
					}
					else
					{
						unset($this->project_system['system'][$i]);
						$i--;
						$this->project_system['gvs']['1']['q'] = $qq;
						$this->project_system['gvs']['1']['g'] = round($qq*1000/($this->project_parameter['t3']-5), 3);
						$this->project_system['gvs']['1']['short'] = '3';
						$this->project_system['gvs']['1']['title'] = 'ГВС';
						$this->project_system['gvs']['1']['type'] = $value['name'];
					}
				break;
				//система вентиляции
				case "vent":
					$this->project_system['system'][$i]['g'] = round($qq*1000/($this->project_parameter['t1']-$this->project_parameter['t2']), 3);
					$this->project_system['system'][$i]['sort'] = '2';
					$sys_title_array['vent'] = 'вентиляция';
					$sum_podpitka = $sum_podpitka+$qq*8.6;
				break;
				//система отопления
				case "otop":
					$this->project_system['system'][$i]['g'] = round($qq*1000/($this->project_parameter['t1']-$this->project_parameter['t2']), 3);
					$this->project_system['system'][$i]['sort'] = '1';
					$sys_title_array['otop'] = 'отопление';					
					$vent_only = false;
					$sum_podpitka = $sum_podpitka+$qq*11.7;
				break;
			}
			if(isset($sys_title_array[$value['name']]))
			{
				$this->project_system['system'][$i]['title'] = mb_ucfirst($sys_title_array[$value['name']]);
			}
			if(isset($this->project_system['system'][$i]['g']))
			{
				$g_all = $g_all+$this->project_system['system'][$i]['g'];	
			}
			//только для Питера
			if($this->tso == '2')
			{					
				if($value['name'] != 'gvs')
				{				
					$g_min_sum = $g_min_sum + round($this->project_system['system'][$i]['g']*0.5, 2);
					$g_max_sum = $g_max_sum + round($this->project_system['system'][$i]['g']*1.25, 2);
				}
				else			
				{				
					$g_min_sum = $g_min_sum + round($this->project_system['system'][$i]['g']*0.1, 2);
					$g_max_sum = $g_max_sum + $this->project_system['system'][$i]['g'];
				}				
			}
		}	
		//
		
		//строка с перечислением всех систем
		$this->project_system['sys_list_str'] = (($i > 1) ? 'Системы: ' : 'Система: ').implode(', ', $sys_title_array).',';
		$this->project_system['sys_count'] = $i;
		//если из систем присутствует только вентиляция
		$this->project_system['vent_only'] = $vent_only; 
		//общая нагрузка на все системы
		$this->project_system['g']['all'] = $g_all; 
		if($this->tso == '2')
		{
			$this->project_system['g']['all'] = $g_max_sum; 
		}
		//обнуляем нагрузки на схема 2,3,4
		$this->project_system['g']['3'] = '0'; 			
		$this->project_system['g']['4'] = '0'; 			
		$this->project_system['g']['94'] = '0';
		//расчет нагрузки на 2 схему 
		if($this->scheme == '2')
		{
			$this->project_system['g']['94'] = round($sum_podpitka/6, 3);
		}
		//расчет нагрузки на 3 схему 
		if($this->scheme == '3')
		{
			$this->project_system['g']['3'] = $this->project_system['gvs']['1']['g'];
		}
		//расчет нагрузки на 3 схему 
		if($this->scheme == '4')
		{
			$this->project_system['g']['3'] = $this->project_system['gvs']['1']['g'];
			$this->project_system['g']['4'] = $this->project_system['gvs']['1']['g']*0.3;
		}
		
	}
	
	private function getDnByG($g)
	{
		//расчет диаметра трубы от напора
		$dn = 0;
		$ksh = 0.5; //коофицент широховатости
		$kg = 958.4; //плотность			
		foreach($this->sprav['dn'] as $k => $v) 
		{
			$da = 1/pow((1.14+2*log10(($v/1000)/($ksh/1000))), 2);
			$h = 0.00638*$da*pow($g,2)/(pow($v/1000, 5)*$kg);
			if($h>4&&$h<18)
			{
				$dn = $v;
				break;
			}		
		}
		return $dn;
	}
	
	private function getMonthRashod($q, $type='all')
	{
		$return = array();
		$snip = $this->sprav['t_snip'][$this->sprav['t_snip_by_tso'][$this->tso]];
		if($type != 'ГВС')
		{
			foreach($snip as $key => $value) 
			{
				$return[$key] = round(($q*($value['t_room'] - $value['t_sr'])/($value['t_room'] - $value['t_min']))*$value['day_count'], 2);
			}
		}
		else
		{
			for ($key = 1; $key <= 12; $key++) 
			{
				$return[$key] = round($q*0.75*4+$q*0.5*12+$q*0.02*8*cal_days_in_month(CAL_GREGORIAN, $key, 2019), 2);
			}
		}
		return $return;
	}
	
	private function getLuchByDn($dn, $ddt, $d, $lrashodomer)
	{
		global $core, $db;
		if($this->tso == '1')
		{
			$luch = $d*6;
			if($luch<240)
			{
				$luch = 240;
			}
			$luch = $luch+$lrashodomer;
			//спасибо Андрею
			if($d == '65')
			{
				$luch = 400+$lrashodomer;
			}
			elseif($d == '80')
			{
				$luch = 500+$lrashodomer;
			}
		}
		else
		{
			$parametr = array();
			$parametr['man'] = $this->project_quipment['preborRashod']['mid'];
			$parametr['category'] = '275';
			$parametr['attribute'] = array('15' => $ddt,
										   '16' => $d,
										   '17' => $dn);
										   
			//print_r($parametr);
			$catalog = $this->getCatalog($parametr);
			//print_r($catalog);
			$attribute	= json_decode($catalog['attribute'], true);
			$luch = $attribute['254']+$lrashodomer;
		}
		return $luch;
		
	}
	
	private function getLdtByDdt($ddt)
	{
		//расчет длины датчика температуры из диаметра трубопровода КИП
		if($ddt == 65)
		{
			$ldt = 80;
		}
		elseif(($ddt == 80)||($ddt == 100))
		{
			$ldt = 100;
		}
		elseif(($ddt == 125)||($ddt == 150))
		{
			$ldt = 120;
		}
		elseif($ddt == 200)
		{
			$ldt = 160;
		}
		else
		{
			$ldt = 0;	
		}
		return $ldt;
	}
	
	public function getDn()
	{		
		//обнуление диаметров на всех трубах
		$this->project_dn['all']['dn'] = '0';
		$this->project_dn['3']['dn'] = '0';
		$this->project_dn['4']['dn'] = '0';
		$this->project_dn['94']['dn'] = '0';
		$this->project_dn['all']['ddt'] = '0';
		$this->project_dn['3']['ddt'] = '0';
		$this->project_dn['4']['ddt'] = '0';
		$this->project_dn['94']['ddt'] = '0';
		$this->project_dn['all']['dn_str'] = 'Ду=0 мм';
		$this->project_dn['3']['dn_str'] = 'Ду=0 мм';
		$this->project_dn['4']['dn_str'] = 'Ду=0 мм';
		$this->project_dn['94']['dn_str'] = 'Ду=0 мм';
		//расчет диаметра основного трубопровода		
		$this->project_dn['all']['dn'] = $this->getDnByG($this->project_system['g']['all']);
		$this->project_dn['all']['dn_str'] = $dn1 = 'Ду='.$this->project_dn['all']['dn'].' мм';
		//диаметр трубы под КИП основного трубопровода (ddt 1)
		$this->project_dn['all']['ddt'] = 65;
		if($this->project_dn['all']['dn'] > 65)
		{
			$this->project_dn['all']['ddt'] = $this->project_dn['all']['dn'];
		}
		//расчет диаметра трубопровода на подпитку (dn 94)
		//диаметр трубы под КИП основного трубопровода (ddt 94)
		if($this->scheme == '2')
		{
			//расчет диаметра основного трубопровода	
			if($this->getDnByG($this->project_system['g']['94']) >='20')
			{
				$this->project_dn['94']['dn'] = $this->getDnByG($this->project_system['g']['94']);
			}
			else
			{
				$this->project_dn['94']['dn'] = '20';
			}
			$this->project_dn['94']['dn_str'] = $dn1 = 'Ду='.$this->project_dn['94']['dn'].' мм';
			//диаметр трубы под КИП основного трубопровода (ddt 1)
			$this->project_dn['94']['ddt'] = $this->project_dn['94']['dn'];
		}		
		//расчет диаметра трубопровода dn 3
		//диаметр трубы под КИП трубопровода ddt 4
		if($this->scheme == '3')
		{
			//расчет диаметра основного трубопровода		
			$this->project_dn['3']['dn'] = $this->getDnByG($this->project_system['g']['3']);
			$this->project_dn['3']['dn_str'] = $dn1 = 'Ду='.$this->project_dn['3']['dn'].' мм';
			//диаметр трубы под КИП основного трубопровода (ddt 1)
			$this->project_dn['3']['ddt'] = 65;
			if($this->project_dn['3']['dn'] > 65)
			{
				$this->project_dn['3']['ddt'] = $this->project_dn['3']['dn'];
			}
		}
		
		//расчет диаметра трубопровода на подпитку (dn 4)
		//диаметр трубы под КИП основного трубопровода (ddt 4)
		if($this->scheme == '4')
		{
			//расчет диаметра основного трубопровода		
			$this->project_dn['3']['dn'] = $this->getDnByG($this->project_system['g']['3']);
			$this->project_dn['3']['dn_str'] = $dn1 = 'Ду='.$this->project_dn['3']['dn'].' мм';
			//диаметр трубы под КИП основного трубопровода (ddt 1)
			$this->project_dn['3']['ddt'] = 65;
			if($this->project_dn['3']['dn'] > 65)
			{
				$this->project_dn['3']['ddt'] = $this->project_dn['3']['dn'];
			}
			//расчет диаметра основного трубопровода		
			$this->project_dn['4']['dn'] = $this->getDnByG($this->project_system['g']['4']);
			$this->project_dn['4']['dn_str'] = $dn1 = 'Ду='.$this->project_dn['4']['dn'].' мм';
			//диаметр трубы под КИП основного трубопровода (ddt 1)
			$this->project_dn['4']['ddt'] = 65;
			if($this->project_dn['4']['dn'] > 65)
			{
				$this->project_dn['4']['ddt'] = $this->project_dn['4']['dn'];
			}
	
		}
	}
	
	public function generateRashodomer($g_all, $dn, $ddt, $t1, $t2, $p1, $p2, $system)
	{
		global $core, $db;
		//первичный расходомер для подбора
		$dnr_min = $dn/2.8;	
		$i = 0;
		foreach($this->sprav['dn'] as $k => $v) 
		{
			if($dnr_min <= $v)
			{
				$dnr_min = $v;
				$dnr_min_key = $k;
				break;
			}
		}
		if($this->tso == '1')
		{
			$const_min_all = 0.1;
			$const_min_gvs = 0.1;
		}
		elseif($this->tso == '2')
		{
			$const_min_all = 0.5;	
			$const_min_gvs = 0.1;	
		}
		else
		{
			$const_min_all = 0.1;
			$const_min_gvs = 0.1;
		}
		
		$where = '';
		//установка индивидуальных значение для определенного типа расходомера
		switch($this->project_quipment['preborRashod']['select']) 
		{       
			case "1":							
				if($dnr_min<64)
				{
					$where = "AND JSON_VALUE(`attribute` , '$.10') IN ('Сэндвич')";
				}
				else
				{
					$where = "AND JSON_VALUE(`attribute` , '$.64') IN ('IP65')";
				}
				if($dnr_min<20)
				{
					$dnr_min = 20;
				}			
			break;			
		}
		//подбор расходомера по его параметрам
		do 
		{
			switch($this->project_quipment['preborRashod']['select']) 
			{       
				case "1":							
					if($dnr_min<64)
					{
						$where = "AND JSON_VALUE(`attribute` , '$.10') IN ('Сэндвич')";
					}
					else
					{
						$where = "AND JSON_VALUE(`attribute` , '$.64') IN ('IP65')";
					}			
				break;			
			}	
			$i++;					
			$query_rashodomer = $db->query("SELECT * FROM " . DB_PREFIX . "_catalog WHERE active = '1' AND hidden = '0' AND man = '".$this->project_quipment['preborRashod']['mid']."' AND (category LIKE '%,".$this->project_quipment['preborRashod']['cat'].",%') AND JSON_VALUE(`attribute` , '$.4') IN ('".$dnr_min."')".$where);
			//echo "SELECT * FROM " . DB_PREFIX . "_catalog WHERE active = '1' AND hidden = '0' AND man = '".$this->project_quipment['preborRashod']['mid']."' AND (category LIKE '%,".$this->project_quipment['preborRashod']['cat'].",%') AND JSON_VALUE(`attribute` , '$.4') IN ('".$dnr_min."')".$where;
			if($db->numRows($query_rashodomer) > 0) 
			{
			
				$qmax = '';
				$rsArray = array();
				while($rashodomer = $db->getRow($query_rashodomer)) 
				{
					$attribute = json_decode($rashodomer['attribute'], true);
					$spec = json_decode($rashodomer['spec'], true);
					$attribute_qmax  = str_replace(',', '.', $attribute['189']);
					//echo $g_all/$attribute_qmax.'<br>';
					if ($g_all/$attribute_qmax<0.61)
					{		
				
						
						$rsArray[$rashodomer['id']]['razn'] = 1000;
						if($system == 'system')
						{
							foreach ($this->project_system[$system] as $key => $value) 
							{
								//проверка если есть не только вентиляция то вентиляцию не учитывать (только при первой схеме)
								if($this->project_system['vent_only'] || ($value['title'] != 'Вентиляция'))
								{
									if($value['title'] != 'ГВС')
									{
										$min = $const_min_all;
									}
									else
									{
										$min = $const_min_gvs;	
									}
									if ($rsArray[$rashodomer['id']]['razn'] > $value['g']*$min-$attribute['187'])
									{								
										$luch = $this->getLuchByDn($dn, $ddt, $dnr_min, $attribute['192']);
										$rsArray[$rashodomer['id']]['razn'] = $value['g']*$min-$attribute['187'];										
										$rsArray[$rashodomer['id']]['qmax'] = $attribute_qmax;
										$rsArray[$rashodomer['id']]['attribute'] = $attribute;
										$rsArray[$rashodomer['id']]['luch'] = $luch;
										$rsArray[$rashodomer['id']]['spec'] = $spec;
										if((empty($qmax))||($qmax > $attribute_qmax))
										{
											$qmax = $attribute_qmax;
										}
									}
								}
							}	
						}
						elseif($system == 'gvs')
						{
							if ($rsArray[$rashodomer['id']]['razn'] > $this->project_system['gvs']['1']['g']*$const_min_gvs-$attribute['187'])
							{								
								$luch = $this->getLuchByDn($dn, $ddt, $dnr_min, $attribute['192']);
								$rsArray[$rashodomer['id']]['razn'] = $this->project_system['gvs']['1']['g']*$const_min_gvs-$attribute['187'];								
								$rsArray[$rashodomer['id']]['qmax'] = $attribute_qmax;
								$rsArray[$rashodomer['id']]['attribute'] = $attribute;
								$rsArray[$rashodomer['id']]['luch'] = $luch;
								$rsArray[$rashodomer['id']]['spec'] = $spec;
								if((empty($qmax))||($qmax > $attribute_qmax))
								{
									$qmax = $attribute_qmax;
								}
							}
						}
						elseif($system == '94')
						{
							if ($rsArray[$rashodomer['id']]['razn'] > $this->project_system['g']['94']-$attribute['187'])
							{
								$luch = $this->getLuchByDn($dn, $ddt, $dnr_min, $attribute['192']);
								$rsArray[$rashodomer['id']]['razn'] = $this->project_system['g']['94']-$attribute['187'];
								$rsArray[$rashodomer['id']]['qmax'] = $attribute_qmax;
								$rsArray[$rashodomer['id']]['attribute'] = $attribute;
								$rsArray[$rashodomer['id']]['luch'] = $luch;
								$rsArray[$rashodomer['id']]['spec'] = $spec;
								if((empty($qmax))||($qmax > $attribute_qmax))
								{
									$qmax = $attribute_qmax;
								}
							}
						}
						
					}
					
				}
				//echo '<pre>';
				//print_r($rsArray);
				$check = '';
				$chek_id = '';
				foreach ($rsArray as $key => $value) 
				{
					if(($value['razn']>=0)&&($qmax == $value['qmax']))
					{
						if(empty($check))
						{
							$check = $value['razn'];
							$check_id = $key;
							$check_attribute = $value['attribute'];
							$check_luch = $value['luch'];
							$check_spec = $value['spec'];
						}
						elseif($check>$value['razn'])
						{
							$check = $value['razn'];
							$check_id = $key;
							$check_attribute = $value['attribute'];
							$check_luch = $value['luch'];
							$check_spec = $value['spec'];
							
						}
					}
				}	
			}	
			$rashodomer_excel = 'РС'.$check_attribute['4'].'-'.$this->dotsReplace($check_attribute['189']);
			//echo $rashodomer_excel;
			$konfuz1 = $check_attribute['4'].'-'.$ddt;
			$konfuz2 = $check_attribute['4'].'-'.$dn;
			$diffuz1 = $check_attribute['4'].'-'.$dn;
			$diffuz2 = $check_attribute['4'].'-'.$ddt;
			$raschet_tpl = ROOT.'/files/tpl/metering/raschet.xlsx';
			$raschet_calc = ROOT.'/files/tpl/metering/raschet_calc.xlsx';
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
			$spreadsheet = $reader->load($raschet_tpl);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('F27', $g_all, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('H27', $g_all, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('F28', $t1, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('H28', $t2, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('F29', $p1*10, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('H29', $p2*10, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('F30', $this->dotsReplace($rashodomer_excel));
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('H30', $this->dotsReplace($rashodomer_excel));
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('F31', $this->dotsReplace($konfuz1));
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('H31', $this->dotsReplace($konfuz2));
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('F32', $this->dotsReplace($diffuz1));
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('H32', $this->dotsReplace($diffuz2));
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('F34', $check_luch, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$spreadsheet->setActiveSheetIndex(2)->setCellValueExplicit('H34', $check_luch, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$oCells = $spreadsheet->getSheet(2)->getCellCollection();		
			$excel_sum_1 = round($oCells->get('F58')->getCalculatedValue(), 4);
			//echo $excel_sum_1;
			if($excel_sum_1>0.5)
			{
				$dnr_min_key++;
				$dnr_min = $this->sprav['dn'][$dnr_min_key];
			}
			else
			{
				$excel['d0_1'] = $oCells->get('F36')->getCalculatedValue();
				$excel['d1_1'] = $oCells->get('F37')->getCalculatedValue();
				$excel['d2_1'] = $oCells->get('F38')->getCalculatedValue();
				$excel['a1_1'] = round($oCells->get('F39')->getCalculatedValue(), 1);
				$excel['a2_1'] =  round($oCells->get('F40')->getCalculatedValue(), 1);
				$excel['d0_2'] = $oCells->get('H36')->getCalculatedValue();
				$excel['d1_2'] = $oCells->get('H37')->getCalculatedValue();
				$excel['d2_2'] = $oCells->get('H38')->getCalculatedValue();
				$excel['a1_2'] = round($oCells->get('H39')->getCalculatedValue(), 1);
				$excel['a2_2'] = round($oCells->get('H40')->getCalculatedValue(), 1);
				$excel['r_1'] =  round($oCells->get('F42')->getCalculatedValue(), 2);
				$excel['q_1'] =  round($oCells->get('F43')->getCalculatedValue(), 4);
				$excel['v0_1'] =  round($oCells->get('F44')->getCalculatedValue(), 4);
				$excel['v1_1'] =  round($oCells->get('F45')->getCalculatedValue(), 4);
				$excel['v2_1'] =  round($oCells->get('F46')->getCalculatedValue(), 4);	
				$excel['r_2'] =  round($oCells->get('H42')->getCalculatedValue(), 2);
				$excel['q_2'] =  round($oCells->get('H43')->getCalculatedValue(), 4);
				$excel['v0_2'] =  round($oCells->get('H44')->getCalculatedValue(), 4);
				$excel['v1_2'] =  round($oCells->get('H45')->getCalculatedValue(), 4);
				$excel['v2_2'] =  round($oCells->get('H46')->getCalculatedValue(), 4);
				$excel['xtr_1'] = round($oCells->get('F49')->getCalculatedValue(), 4);
				$excel['dhk_1'] = round($oCells->get('F50')->getCalculatedValue(), 4);
				$excel['xtr_2'] = round($oCells->get('H49')->getCalculatedValue(), 4);
				$excel['dhk_2'] = round($oCells->get('H50')->getCalculatedValue(), 4);
				$excel['l_1'] = round($oCells->get('F52')->getCalculatedValue(), 4);
				$excel['dhp_1'] = round($oCells->get('F53')->getCalculatedValue(), 4);
				$excel['l_2'] = round($oCells->get('H52')->getCalculatedValue(), 4);
				$excel['dhp_2'] = round($oCells->get('H53')->getCalculatedValue(), 4);
				$excel['xrp_1'] = round($oCells->get('F55')->getCalculatedValue(), 3);
				$excel['xtrd_1'] = round($oCells->get('F56')->getCalculatedValue(), 4);
				$excel['dhd_1'] = round($oCells->get('F57')->getCalculatedValue(), 4);
				$excel['sum_1'] = round($oCells->get('F58')->getCalculatedValue(), 4);
				$excel['total_1'] = round($oCells->get('F59')->getCalculatedValue(), 4);
				$excel['xrp_2'] = round($oCells->get('H55')->getCalculatedValue(), 3);
				$excel['xtrd_2'] = round($oCells->get('H56')->getCalculatedValue(), 4);
				$excel['dhd_2'] = round($oCells->get('H57')->getCalculatedValue(), 4);
				$excel['sum_2'] = round($oCells->get('H58')->getCalculatedValue(), 4);
				$excel['total_2'] = round($oCells->get('H59')->getCalculatedValue(), 4);
			}
			if ($i>8)
			{
				echo 'error';
				exit;
			}
			//echo '<br>'.$excel_sum_1.'<br>';
			
		} while (($excel_sum_1 > 0.5));
		
		
		
		$return_array = array();
		$return_array['check'] = $check;
		$return_array['check_id'] = $check_id;
		$return_array['check_attribute'] = $check_attribute;
		$return_array['check_luch'] = $check_luch;
		$return_array['check_spec'] = $check_spec;
		$return_array['check_excel'] = $excel;
		
		$return_array['check_parametr']['rashodomer_excel']['1'] = $rashodomer_excel;
		$return_array['check_parametr']['rashodomer_excel']['2'] = $rashodomer_excel;
		$return_array['check_parametr']['konfuz']['1'] = $konfuz1;
		$return_array['check_parametr']['konfuz']['2'] = $konfuz2;
		$return_array['check_parametr']['diffuz']['1'] = $diffuz1;
		$return_array['check_parametr']['diffuz']['2'] = $diffuz2;
		
		return $return_array;
	}
	
	private function structureSpecification($i, $catalog, $count)
	{		
		$sku = $catalog['sku'];
		$unit =  (isset($catalog['unit_sname']) ? $catalog['unit_sname'] : '');
		$name = $catalog['title'];
		$model = ' ';
		$manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
		$spec_array = json_decode($catalog['spec'], true);
		$weight	 = json_decode($catalog['weight'], true);
		if (!empty($spec_array['1']))
		{
			$name = $spec_array['1'];
		}
		if (!empty($spec_array['2']))
		{
			$model = str_replace("&lt;br&gt;", "<w:br/>", $spec_array['2']);
		}
		if (!empty($spec_array['3']))
		{
			$manufacter = $spec_array['3'];
		}
		$weight_good = '';
		if (!empty($weight['1']))
		{
			$weight_good = $weight['1'];
		}
		$order_data_spec_tm =  array(
			'tm_n'        		=> $i,
			'tm_title' 			=> $name,			
			'tm_model' 			=> $model,			
			'tm_sku' 			=> $sku,			
			'tm_manufacter' 	=> $manufacter,			
			'tm_unit' 			=> $unit,			
			'tm_count' 			=> $count,			
			'tm_weight' 		=> $weight_good,			
		);
		return $order_data_spec_tm;
	}		
    
	private function perehod($dn1, $dn2, $type = 'Без покрытия') 
	{
		global $core, $db;
		$query_perehod = $db->query("SELECT * FROM " . DB_PREFIX . "_catalog WHERE active = '1' AND hidden = '0' AND (category LIKE '%,334,%') AND JSON_VALUE(`attribute` , '$.108') IN ('".$type."')");
		$rtan = 99999;
		$return_array =array();
		if($db->numRows($query_perehod) > 0) 
		{
			while($perehod = $db->getRow($query_perehod)) 			
			{			
				$attribute = json_decode($perehod['attribute'], true);
				$spec = json_decode($perehod['spec'], true);
				if(($attribute['4'] == $dn1)&&($attribute['98'] == $dn2))
				{
					$return_array[] = $perehod;
					return($return_array);
				}
				elseif($dn1==$dn2)
				{
					return false;	
				}
				elseif(($attribute['4'] == $dn1))
				{
					$query_perehod2 = $db->query("SELECT * FROM " . DB_PREFIX . "_catalog WHERE active = '1' AND hidden = '0' AND (category LIKE '%,334,%') AND JSON_VALUE(`attribute` , '$.108') IN ('".$type."')");
					$tan1 = rad2deg(atan($attribute['248']/(($dn1-$attribute['98'])/2)));
					while($perehod2 = $db->getRow($query_perehod2)) 			
					{
						$attribute2 = json_decode($perehod2['attribute'], true);
						$spec2 = json_decode($perehod2['spec'], true);
						if(($attribute2['4'] == $attribute['98'])&&($attribute2['98'] == $dn2))
						{
							$tan2 = rad2deg(atan($attribute2['248']/(($attribute['98']-$dn2)/2)));
							if($rtan >= abs($tan1-$tan2))
							{
								$rtan = abs($tan1-$tan2);
								$pr1 = $perehod;
								$pr2 = $perehod2;
							}
						}
					}
					
				}
			}
			$return_array[] = $pr1;
			$return_array[] = $pr2;
		}
		return($return_array);
	}

	private function getDnSpByDn($dn)
	{
		if($dn <=25)
		{
			$dn_sp  = '15';
		}
		elseif(($dn == 32)||($dn == 40))
		{
			$dn_sp = '20';
		}
		else
		{
			$dn_sp = '25';
		}
		return $dn_sp;
	}
	
	private function getTermometrByT($t)
	{
		$termometr = 'От 0 до 100';
		if($t >95)
		{
			$termometr = 'От 0 до 150';
		}		
		return $termometr;
	}
	
	private function getTermometrDlinaByDDT($ddt)
	{
		$termometr_dlina = '66';
		if($ddt >= 100)
		{
			$termometr_dlina = '103';
		}
		return $termometr_dlina;
	}
	
	private function getLbobLosnByDDT($ddt)
	{
		if($ddt != '0')
		{
			$lbob_array = array(30,40,50,60);
			$ddt_n = $this->sprav['dn_n'][$ddt];		
			if($this->tso == '1')
			{				
				$lbob_spec = '';				
				$losn = 15;
				$lbob = $this->getLdtByDdt($ddt) - $ddt_n/2-7-15;
				foreach ($lbob_array as $key => $value) 
				{
					if($lbob<=$value)
					{
						$lbob_spec = $value;
						break;			
					}
				}
			} 
			else
			{
				$losn_temp = 16;
				foreach ($lbob_array as $key => $value) 
				{
					$losn_pre = $this->getLdtByDdt($ddt) - $ddt_n/2+7-$value;
					if(abs(15-$losn_pre) < $losn_temp)
					{
						$losn_temp = abs(15-$losn_pre);
						$losn = $losn_pre;
						$lbob = $value;			
						$lbob_spec = $value;			
					}
				}
			}			
			$return_array['lbob'] = $lbob;
			$return_array['lbob_spec'] = $lbob_spec;
			$return_array['losn'] = $losn;
		}
		else
		{
			$return_array['lbob'] = '0';
			$return_array['lbob_spec'] = '';
			$return_array['losn'] = '0';
		}
		return $return_array;
	}
	
	public function getResult()
	{	
		//массив для таблицы используемых систем (страница 4)
		$system_list = array();	
		$system_list_rashod = array();
		$system_list_q = array();
		$system_list_g = array();
		$system_list_gmax = array();
		$system_list_potreb_day = array();
		$system_list_potreb_month = array();
		$i = 0;		
		$g_min_sys = 10000;
		$g_min_sum = 0;
		$g_max_sum = 0;
		foreach ($this->project_system['system'] as $key => $value) 
		{	
			if($value['title'] != "ГВС")
			{
				$ttt = $this->project_parameter['t1'].'/'.$this->project_parameter['t2'];
			}
			else
			{
				$ttt = '70/30';
			}
			$i++;
			
			if($this->tso == '1')
			{
				//только для ижевска
				$system_list[] =  array(
					'n'        	=> $i,
					'title' 	=> $value['title'],
					'q'     	=> $value['q'],
					't1t2'     	=> $ttt,
					'g'     	=> ((($value['title'] == 'Вентиляция')&&(!$this->project_system['vent_only'])) ? $value['g'] : $value['g'].'/<w:br/>'.($value['g']*0.1).'*'),
				);
			}
			else
			{
				$system_list[] =  array(
					'n'        	=> $i,
					'title' 	=> $value['title'],
					'q'     	=> $value['q'],
					't1t2'     	=> $ttt,
					'g'     	=> $value['g'],
				);
			}
			if(($g_min_sys>$value['g'])&&(($value['title'] != 'Вентиляция')||$this->project_system['vent_only']))
			{
				$g_min_sys = $value['g'];
			}
			//только для Питера
			if($this->tso == '2')
			{					
				if($value['title'] != 'ГВС')
				{
					$system_list_rashod[] =  array(
						'sys_title' 	=> $value['title'],
						'q'     	=> $value['q'],
						'g1'     	=> $value['g'],
						'g2'     	=> $value['g'],
						'g3'     	=> $value['g'],
					);
					$system_list_gmax[] =  array(
						'sys_title_gmax' 	=> $value['title'],
						'g_min_max'    		=> round($value['g']*0.5, 2).'<w:br/>'.round($value['g']*1.25, 2),						
					);
					$system_list_potreb_day[] =  array(
						'sys_potreb_day_title' 	=> $value['title'],
						'qs1'    				=> round($value['q']*24, 2),						
						'qs2'    				=> round($value['q']*24, 2),						
						'qs3'    				=> round($value['q']*24, 2),						
						'qs4'    				=> round($value['q']*24, 2),						
						'qs5'    				=> round($value['q']*24, 2),						
					);
					$g_min_sum = $g_min_sum + round($value['g']*0.5, 2);
					$g_max_sum = $g_max_sum + round($value['g']*1.25, 2);					
				}
				else			
				{
					$system_list_rashod[] =  array(
						'sys_title' 	=> $value['title'],
						'q'     	=> $value['q'],
						'g1'     	=> round($value['g']*0.75, 2),
						'g2'     	=> round($value['g']*0.5, 2),
						'g3'     	=> round($value['g']*0.02, 2),
					);
					$system_list_gmax[] =  array(
						'sys_title_gmax' 	=> $value['title'],
						'g_min_max'    		=> round($value['g']*0.1, 2).'<w:br/>'.$value['g'],						
					);				
					$system_list_potreb_day[] =  array(
						'sys_potreb_day_title' 	=> $value['title'],
						'qs1'    				=> round($value['q']*0.75*4+$value['q']*0.5*12+$value['q']*0.02*8, 2),						
						'qs2'    				=> round($value['q']*0.75*4+$value['q']*0.5*12+$value['q']*0.02*8, 2),						
						'qs3'    				=> round($value['q']*0.75*4+$value['q']*0.5*12+$value['q']*0.02*8, 2),						
						'qs4'    				=> round($value['q']*0.75*4+$value['q']*0.5*12+$value['q']*0.02*8, 2),						
						'qs5'    				=> round($value['q']*0.75*4+$value['q']*0.5*12+$value['q']*0.02*8, 2),						
					);
					$g_min_sum = $g_min_sum + round($value['g']*0.1, 2);
					$g_max_sum = $g_max_sum + $value['g'];
				}
				$system_list_q[] =  array(
					'sys_title_q' 	=> $value['title'],
					'q'     		=> $value['q'],						
				);
				$system_list_g[] =  array(
					'sys_title_g' 	=> $value['title'],
					'g'     		=> $value['g'],						
				);
				$monthRashod = $this->getMonthRashod($value['q'], $value['title']);
				$system_list_potreb_month[] =  array(
						'sys_potreb_month_title' 	=> $value['title'],
						'm1'    						=> $monthRashod['1'],								
						'm2'    						=> $monthRashod['2'],								
						'm3'    						=> $monthRashod['3'],								
						'm4'    						=> $monthRashod['4'],								
						'm5'    						=> $monthRashod['5'],								
						'm6'    						=> $monthRashod['6'],								
						'm7'    						=> $monthRashod['7'],								
						'm8'    						=> $monthRashod['8'],								
						'm9'    						=> $monthRashod['9'],								
						'm10'    						=> $monthRashod['10'],								
						'm11'    						=> $monthRashod['11'],								
						'm12'    						=> $monthRashod['12'],								
				);
				
			}
		}
		$g_min_sys = $g_min_sys*0.1;
		$this->result['system_list'] = $system_list;
		$this->result['system_list_rashod'] = $system_list_rashod;
		$this->result['system_list_q'] = $system_list_q;
		$this->result['system_list_g'] = $system_list_g;
		$this->result['system_list_gmax'] = $system_list_gmax;
		$this->result['system_list_potreb_day'] = $system_list_potreb_day;
		$this->result['system_list_potreb_month'] = $system_list_potreb_month;
		$this->result['g_min_sys_piter'] = $g_min_sum;
		$this->result['g_max_sys_piter'] = $g_max_sum;
		//минимальный расход по системам
		$this->result['g_min_sys'] = $g_min_sys;
		//располагаемый напор
		$this->result['dp']['all'] = ($this->project_parameter['p1']-$this->project_parameter['p2'])*100;
		$this->result['dp']['3'] = $this->project_parameter['p3']*100;
		$this->result['dp']['4'] = $this->project_parameter['p4']*100;
		$this->result['dp']['94'] = '0';
		//подбор расходомеров на основной трубопровод
		$this->result['rashodomer']['all'] = $this->generateRashodomer($this->project_system['g']['all'], 
																 $this->project_dn['all']['dn'], 
																 $this->project_dn['all']['ddt'], 
																 $this->project_parameter['t1'], 
																 $this->project_parameter['t2'], 
																 $this->project_parameter['p1'], 
																 $this->project_parameter['p2'],
																 'system');
		//вычисление напора на узле
		$this->result['sum_dp']['all'] = round($this->result['rashodomer']['all']['check_excel']['sum_1'] + $this->result['rashodomer']['all']['check_excel']['sum_2'], 2);
		//обнуляем все остальные возможные расходомеры
		$this->result['rashodomer']['3'] = array();
		$this->result['rashodomer']['4'] = array();
		$this->result['rashodomer']['94'] = array();
		//обнуляем снижение напора на остальные раходамеры
		$this->result['sum_dp']['3'] = '0';
		$this->result['sum_dp']['4'] = '0';
		$this->result['sum_dp']['94'] = '0';
		//вычисляем длину датчика температуры на основной трубопровод
		$this->result['ldt']['all'] = $this->getLdtByDdt($this->project_dn['all']['ddt']);
		//обнуляем длину датчика температуры
		$this->result['ldt']['3'] = '0';
		$this->result['ldt']['4'] = '0';
		$this->result['ldt']['94'] = '0';
		//заполняем данные на подпитку 2-ая схема
		if($this->scheme == '2')
		{
			//вычисляем расходомер
			$this->result['rashodomer']['94'] = $this->generateRashodomer($this->project_system['g']['94'], 
																 $this->project_dn['94']['dn'], 
																 $this->project_dn['94']['ddt'], 
																 $this->project_parameter['t2'], 
																 $this->project_parameter['t1'], 
																 $this->project_parameter['p2'], 
																 $this->project_parameter['p1'],
																 '94');
			//вычисляем снижение напора													 
			$this->result['sum_dp']['94'] = $this->result['rashodomer']['94']['check_excel']['sum_1'];
			//вычисляем длину датчика тмпературы
			$this->result['ldt']['94'] = $this->getLdtByDdt($this->project_dn['94']['ddt']);
		}
		//заполняем данные на 3-х трубную систему
		if($this->scheme == '3')
		{
			//вычисляем расходомер
			$this->result['rashodomer']['3'] = $this->generateRashodomer($this->project_system['g']['3'], 
																 $this->project_dn['3']['dn'], 
																 $this->project_dn['3']['ddt'], 
																 $this->project_parameter['t3'], 
																 $this->project_parameter['t2'], 
																 $this->project_parameter['p3'], 
																 $this->project_parameter['p2'],
																 'gvs');
			//вычисляем снижение напора													 
			$this->result['sum_dp']['3'] = $this->result['rashodomer']['3']['check_excel']['sum_1'];
			//вычисляем длину датчика тмпературы
			$this->result['ldt']['3'] = $this->getLdtByDdt($this->project_dn['3']['ddt']);
		}
		//заполняем расходомер на 4-х трубную систему
		if($this->scheme == '4')
		{
			//вычисляем расходомер 3 расходомер
			$this->result['rashodomer']['3'] = $this->generateRashodomer($this->project_system['g']['3'], 
																 $this->project_dn['3']['dn'], 
																 $this->project_dn['3']['ddt'], 
																 $this->project_parameter['t3'], 
																 $this->project_parameter['t4'], 
																 $this->project_parameter['p3'], 
																 $this->project_parameter['p4'],
																 'gvs');
			//вычисляем расходомер 4 расходомер
			$this->result['rashodomer']['4'] = $this->generateRashodomer($this->project_system['g']['4'], 
																 $this->project_dn['4']['dn'], 
																 $this->project_dn['4']['ddt'], 
																 $this->project_parameter['t3'], 
																 $this->project_parameter['t4'], 
																 $this->project_parameter['p3'], 
																 $this->project_parameter['p4'],
																 'gvs');
			//вычисляем снижение напора		
			$this->result['sum_dp']['3'] = $this->result['rashodomer']['3']['check_excel']['sum_1'];
			$this->result['sum_dp']['4'] = $this->result['rashodomer']['3']['check_excel']['sum_2'];
			//вычисляем длину датчика температуры
			$this->result['ldt']['3'] = $this->getLdtByDdt($this->project_dn['3']['ddt']);
			$this->result['ldt']['4'] = $this->getLdtByDdt($this->project_dn['4']['ddt']);
		}
		//договорное давление для таблицы программирования
		$this->result['pdog']['1'] = $this->project_parameter['p1']*10+1;
		$this->result['pdog']['2'] = $this->project_parameter['p2']*10+1;
		$this->result['pdog']['3'] = $this->project_parameter['p3']*10+1;
		$this->result['pdog']['4'] = $this->project_parameter['p4']*10+1;
		//вычисляем верхний предел измерения датчика давления в зависимости от давления 
		//также в зависимости от давления выбираем предел измерения у манометра				
		for ($i=0; $i++<4;)
		{
			if(($this->project_parameter['p'.$i]<0.7)&&($this->project_parameter['p'.$i] != 0))
			{
				$this->result['pdd'][$i] = '1,0';
				$this->result['pdd_kgs'][$i] = '10.1972';
				$this->result['manometr'][$i] = '0 – 10';			
			}
			elseif($this->project_parameter['p'.$i] != 0)
			{
				$this->result['pdd'][$i] = '1,6';
				$this->result['pdd_kgs'][$i] = '17.1516';
				$this->result['manometr'][$i] = '0 – 16';
			}
			else
			{
				$this->result['pdd'][$i] = '0';
				$this->result['pdd_kgs'][$i] = '0';
				$this->result['manometr'][$i] = '0';	
			}
		}		
		//получаем диаметр спускника по диаметру основной трубы
		$this->result['dn_sp']['all'] = $this->getDnSpByDN($this->project_dn['all']['dn']);
		$this->result['dn_sp']['3'] = $this->getDnSpByDN($this->project_dn['3']['dn']);
		$this->result['dn_sp']['4'] = $this->getDnSpByDN($this->project_dn['4']['dn']);
		$this->result['dn_sp']['94'] = $this->getDnSpByDN($this->project_dn['94']['dn']);
		//получаем диапазон термометра 
		for ($i=0; $i++<4;)
		{
			$this->result['termometr']['range'][$i] = $this->getTermometrByT($this->project_parameter['t'.$i]);			
		}
		//получаем 10 p
		for ($i=0; $i++<4;)
		{
			$this->result['p10'][$i] = $this->project_parameter['p'.$i]*10;
		}
		//получаем длину термометра 
		$this->result['termometr']['dlina']['1'] = $this->getTermometrDlinaByDDT($this->project_dn['all']['ddt']);
		$this->result['termometr']['dlina']['2'] = $this->getTermometrDlinaByDDT($this->project_dn['all']['ddt']);
		$this->result['termometr']['dlina']['3'] = $this->getTermometrDlinaByDDT($this->project_dn['3']['ddt']);
		$this->result['termometr']['dlina']['4'] = $this->getTermometrDlinaByDDT($this->project_dn['4']['ddt']);
		//получаем длина основания
		$this->result['losn']['all'] = $this->getLbobLosnByDDT($this->project_dn['all']['ddt'])['losn'];
		$this->result['losn']['3'] = $this->getLbobLosnByDDT($this->project_dn['3']['ddt'])['losn'];
		$this->result['losn']['4'] = $this->getLbobLosnByDDT($this->project_dn['4']['ddt'])['losn'];
		//получаем длина бобышки		
		$this->result['lbob']['all'] = $this->getLbobLosnByDDT($this->project_dn['all']['ddt'])['lbob'];
		$this->result['lbob']['3'] = $this->getLbobLosnByDDT($this->project_dn['3']['ddt'])['lbob'];
		$this->result['lbob']['4'] = $this->getLbobLosnByDDT($this->project_dn['4']['ddt'])['lbob'];	
		//получаем длина бобышки для спецификации	
		$this->result['lbob_spec']['all'] = $this->getLbobLosnByDDT($this->project_dn['all']['ddt'])['lbob_spec'];
		$this->result['lbob_spec']['3'] = $this->getLbobLosnByDDT($this->project_dn['3']['ddt'])['lbob_spec'];
		$this->result['lbob_spec']['4'] = $this->getLbobLosnByDDT($this->project_dn['4']['ddt'])['lbob_spec'];
		//получаем таблицу спецификации для датчиков температуры 
		$this->result['dt_spec']['all'] = $this->getSpecificationDT('all');
		$this->result['dt_spec']['3'] = $this->getSpecificationDT('3');
		$this->result['dt_spec']['4'] = $this->getSpecificationDT('4');
		//получаем таблицу спецификации для датчиков давления
		for ($i=0; $i++<4;)
		{
			$this->result['dd_spec'][$i] = $this->getSpecificationDD($i);
		}
	}	
	
	private function resultOut($array)
	{
		$temp = $this->result;
		foreach ($array as $key => $val) {
			if(isset($temp[$val]))
			{
				$temp = $temp[$val];
			}
			else
			{
				$temp = '0';
				break;
			}
		}
		return $temp;		
	}
	
	public function getSpecificationDT($type)
	{
		//спецификация датчиков температуры
		$ldt = $this->result['ldt'][$type];
		$lbob_spec = $this->result['lbob_spec'][$type];
		switch($this->project_quipment['preobTeplo']['select']) 
		{       
			case "1":
				//ктптр-01
				$order_data_spec_dt = array();						
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'        => '1',
						'dt_'.$type.'_marka' 	=> 'ТПТ-1-3-Pt100-AA-4-C-'.$ldt.'<w:br/>ТУ 4211-010-17113168-10',
						'dt_'.$type.'_title'  	=> 'Термометр из платины технический<w:br/>Pt100, класс АА, L='.$ldt.' мм',
						'dt_'.$type.'_u'     	=> 'шт.',
						'dt_'.$type.'_c'     	=> '1*',
						'dt_'.$type.'_p'     	=> 'один из<w:br/>компл.',
					);		
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'      	=> '2',
						'dt_'.$type.'_marka' 	=> 'ГЗ-6,3-8-'.$ldt,
						'dt_'.$type.'_title'  	=> 'Гильза защитная L='.$ldt.' мм',
						'dt_'.$type.'_u'     	=> 'шт.',
						'dt_'.$type.'_c'     	=> '1*',
						'dt_'.$type.'_p'     	=> '',
					);	
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'      	=> '3',
						'dt_'.$type.'_marka' 	=> 'БП-М20x1,5-'.$lbob_spec,
						'dt_'.$type.'_title'  	=> 'Бобышка стальная М20x1,5 L='.$lbob_spec.' мм',
						'dt_'.$type.'_u'     	=> 'шт.',
						'dt_'.$type.'_c'     	=> '1*',
						'dt_'.$type.'_p'     	=> '',
					);					
			break;
			case "2":
				//ТСП-К
				$order_data_spec_dt = array();						
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'        	=> '1',
						'dt_'.$type.'_marka' 		=> 'ТСП-101-Г-'.$ldt.'Pt100-А-4<w:br/>ТУ 4211-033-87875766-2016',
						'dt_'.$type.'_title'   		=> 'Термометр из платины технический<w:br/>Pt100, класс А, L='.$ldt.' мм',
						'dt_'.$type.'_u'     		=> 'шт.',
						'dt_'.$type.'_c'    	 	=> '1*',
						'dt_'.$type.'_p'   	  		=> 'один из<w:br/>компл.',
					);		
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'        	=> '2',
						'dt_'.$type.'_marka' 		=> 'ГЗ-Г.8.'.$ldt,
						'dt_'.$type.'_title'   		=> 'Гильза защитная L='.$ldt.' мм',
						'dt_'.$type.'_u'     		=> 'шт.',
						'dt_'.$type.'_c'    	 	=> '1*',
						'dt_'.$type.'_p'    	 	=> '',
					);	
				$order_data_spec_dt[] =  array(
						'dt_'.$type.'_n'        	=> '3',
						'dt_'.$type.'_marka' 		=> 'Б.П.М20x1,5.'.$lbob_spec,
						'dt_'.$type.'_title'  		=> 'Бобышка стальная М20x1,5 L='.$lbob_spec.' мм',
						'dt_'.$type.'_u'     		=> 'шт.',
						'dt_'.$type.'_c'     		=> '1*',
						'dt_'.$type.'_p'     		=> '',
					);
			break;		
		}
		return $order_data_spec_dt;
	}
	
	public function getSpecificationDD($type)
	{	
		global $core, $db;		
		$pdd = $this->result['pdd'][$type];
		$query_spec = $db->query("SELECT c.sku, c.unit, c.title, c.spec, c.weight, u.unit, u.sname as unit_sname, m.name as manufacture_name FROM ".DB_PREFIX."_catalog as c LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=c.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = c.man) WHERE active = '1' AND hidden = '0' AND man = '".$this->project_quipment['preobDav']['mid']."' AND (category LIKE '%,280,%') AND JSON_VALUE(`attribute` , '$.178') IN ('".str_replace(',','.',$pdd)."') AND JSON_VALUE(`attribute` , '$.242') IN ('".$this->project_quipment['preobDav']['predel']."') LIMIT 1");
		$catalog = $db->getRow($query_spec);
		$dd_title = $catalog['title'];
		$spec_array = json_decode($catalog['spec'], true);
		if (!empty($spec_array['1']))
		{
			$dd_title = $spec_array['1'];
		}
		$order_data_spec_dd = array();		
		if($type == '1')
		{							
			$order_data_spec_dd[] =  array(
							'dd_'.$type.'_n'        	=> '1',
							'dd_'.$type.'_marka' 		=> 'OC100н-02',
							'dd_'.$type.'_title'   		=> 'Отборное устройство угловое',
							'dd_'.$type.'_u'     		=> 'шт.',
							'dd_'.$type.'_c'    	 	=> '1*',
							'dd_'.$type.'_p'   	  		=> '',
						);		
			$order_data_spec_dd[] =  array(
							'dd_'.$type.'_n'        	=> '2',
							'dd_'.$type.'_marka' 		=> '11б27пМ.01',
							'dd_'.$type.'_title'   		=> 'Кран шаровой латунь Ду 15 Ру16 ВР со<w:br/>спускником',
							'dd_'.$type.'_u'     		=> 'шт.',
							'dd_'.$type.'_c'    	 	=> '1*',
							'dd_'.$type.'_p'    	 	=> '',
						);	
		}
		else
		{
			$order_data_spec_dd = array();
			if($this->tso == '1')
			{
				$order_data_spec_dd[] =  array(
								'dd_'.$type.'_n'        	=> '1',
								'dd_'.$type.'_marka' 		=> 'ГОСТ 3262-75',
								'dd_'.$type.'_title'   		=> 'Резьба стальная G 1/2", L=50 мм',
								'dd_'.$type.'_u'     		=> 'шт.',
								'dd_'.$type.'_c'    	 	=> '1*',
								'dd_'.$type.'_p'   	  		=> '',
							);	
			}
			else
			{
				$order_data_spec_dd[] =  array(
								'dd_'.$type.'_n'        	=> '1',
								'dd_'.$type.'_marka' 		=> 'ГОСТ 3262-75',
								'dd_'.$type.'_title'   		=> 'Отвод стальной резьбовой G 1/2", L=200 мм',
								'dd_'.$type.'_u'     		=> 'шт.',
								'dd_'.$type.'_c'    	 	=> '1*',
								'dd_'.$type.'_p'   	  		=> '',
							);	
			}
			$order_data_spec_dd[] =  array(
							'dd_'.$type.'_n'        	=> '2',
							'dd_'.$type.'_marka' 		=> '11б27пМ.01',
							'dd_'.$type.'_title'   		=> 'Кран шаровой латунь Ду 15 Ру16 ВР со<w:br/>спускником',
							'dd_'.$type.'_u'     		=> 'шт.',
							'dd_'.$type.'_c'    	 	=> '1*',
							'dd_'.$type.'_p'    	 	=> '',
						);	
		}
		switch($this->project_quipment['preobDav']['select']) 
		{       
			case "1":
				//сдви 
				$this->project_quipment['preobDav']['marka']['preobDav']['marka'][$type] = 'СДВ-И-'.$pdd.'-4-20мА-D3422-0605-3';
				$order_data_spec_dd[] =  array(
						'dd_'.$type.'_n'        	=> '3',
						'dd_'.$type.'_marka' 		=> 'СДВ-И-'.$pdd.'-4-20мА-D3422-0605-3<w:br/>АГБР.406239.001 ТУ',
						'dd_'.$type.'_title'  		=> $dd_title,
						'dd_'.$type.'_u'     		=> 'шт.',
						'dd_'.$type.'_c'     		=> '1*',
						'dd_'.$type.'_p'     		=> '',
					);
			break;
			case "2":			
				//ддм 
				$pdd_x1000 = floatval(str_replace(',','.',$pdd))*1000; 
				$this->project_quipment['preobDav']['marka'][$type] = 'ДДМ-03Т-'.$pdd_x1000.' ДИ';
				$order_data_spec_dd[] =  array(
						'dd_'.$type.'_n'        	=> '3',
						'dd_'.$type.'_marka' 		=> 'ДДМ-03Т-'.$pdd_x1000.' ДИ<w:br/>ТУ 4211-002-87875767-2009',
						'dd_'.$type.'_title'  		=> $dd_title,
						'dd_'.$type.'_u'     		=> 'шт.',
						'dd_'.$type.'_c'     		=> '1*',
						'dd_'.$type.'_p'     		=> '',
					);
			break;		
			case "3":
				//пдтвх 
				$this->project_quipment['preobDav']['marka'][$type] = 'ПДТВХ-1-02-4/20-'.$pdd.'-0,5';
				$order_data_spec_dd[] =  array(
						'dd_'.$type.'_n'        	=> '3',
						'dd_'.$type.'_marka' 		=> 'ПДТВХ-1-02-4/20-'.$pdd.'-0,5<w:br/>ЮТЛИ.406233.000 ТУ',
						'dd_'.$type.'_title'  		=> $dd_title,
						'dd_'.$type.'_u'     		=> 'шт.',
						'dd_'.$type.'_c'     		=> '1*',
						'dd_'.$type.'_p'     		=> '',
					);
			break;	
		}	
		return $order_data_spec_dd;
	}
	
	private function getCatalog($parametr)
	{
		//функция получения данных из каталога по параметрам товара
		global $core, $db;	
		//сбор параметров для запроса
		$hidden = isset($parametr['hidden']) ? $parametr['hidden'] : '0';
		$where_array['active'] = isset($parametr['active']) ? "AND active ='".$parametr['active']."'" : '';
		$where_array['id'] = isset($parametr['id']) ? "AND c.id ='".$parametr['id']."'" : '';
		$where_array['man'] = isset($parametr['man']) ? "AND man ='".$parametr['man']."'" : '';
		$where_array['category'] = isset($parametr['category']) ? "AND category LIKE '%,".$parametr['category'].",%'" : '';
		if(isset($parametr['attribute']))
		{
			foreach ($parametr['attribute'] as $key => $value) 
			{
				$where_array['atr.'.$key] = "AND JSON_VALUE (`attribute` , '$.".$key."') IN ('".$value."')";
			}
		}
		$where = implode(" ", $where_array);
		//выполнения запроса с учетом параметров
		$query = $db->query("SELECT c.sku, c.unit, c.title, c.spec, c.attribute, c.weight, u.unit, u.sname as unit_sname, m.name as manufacture_name FROM ".DB_PREFIX."_catalog as c LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=c.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = c.man) WHERE hidden = '".$hidden."' ".$where." LIMIT 1");
		$catalog = $db->getRow($query);
		//echo "SELECT c.sku, c.unit, c.title, c.spec, c.attribute, c.weight, u.unit, u.sname as unit_sname, m.name as manufacture_name FROM ".DB_PREFIX."_catalog as c LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=c.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = c.man) WHERE hidden = '".$hidden."' ".$where." LIMIT 1".'<br>';
		return $catalog;
	} 
	public function getSpecificationTM()
	{
		$sp = false;
		if($this->scheme == '1')
		{
			$sp = $this->getSpecificationTMScheme1();
		}
		if($this->scheme == '2')
		{
			$sp = $this->getSpecificationTMScheme2();
		}
		if($this->scheme == '3')
		{
			$sp = $this->getSpecificationTMScheme3();
		}
		if($this->scheme == '4')
		{
			$sp = $this->getSpecificationTMScheme4();
		}	
		return $sp;
	}
	
	public function getSpecificationTMScheme1()
	{
		global $core, $db;
		$order_data_spec_tm = array();
		$i = 0;
		$dd_title ='';
		if($this->tso == '1')
		{
			$order_data_spec_tm[] =  array(
				'tm_n'        		=> '',
				'tm_title' 			=> 'Теплосчётчик Т34М в составе: поз. 1-5',			
				'tm_model' 			=> '',			
				'tm_sku' 			=> '',			
				'tm_manufacter' 	=> 'ТЕРМОТРОНИК',			
				'tm_unit' 			=> '',			
				'tm_count' 			=> '',			
				'tm_weight' 		=> '',			
			);
		}
		if($this->tso == '2')
		{
			$order_data_spec_tm[] =  array(
				'tm_n'        		=> '',
				'tm_title' 			=> 'Теплосчётчик Т34М в составе: поз. 1-6',			
				'tm_model' 			=> '',			
				'tm_sku' 			=> '',			
				'tm_manufacter' 	=> 'ТЕРМОТРОНИК',			
				'tm_unit' 			=> '',			
				'tm_count' 			=> '',			
				'tm_weight' 		=> '',			
			);
		}
		//вычислитель
		$i++;
		$parametr = array();
		$parametr['id'] = $this->project_quipment['preborRashod']['v_id']['1'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//расходомер
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['all']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');			
		//датчик температуры
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['all'],
									   '241' => 'Комплект',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на подаче
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['1']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на обратке
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['2']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		if($this->tso == '2')
		{
			//МП-РС
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['preborRashod']['mid'];
			$parametr['category'] = '275';
			$parametr['attribute'] = array('15' => $this->project_dn['all']['ddt'],
										   '16' => $this->result['rashodomer']['all']['check_attribute']['4'],
										   '17' => $this->project_dn['all']['dn']);			
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		
		if($this->tso == '1')
		{
			//кран шаровой
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['kran']['mid'];
			$parametr['category'] = '286';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '10' =>  'Фланцевое');		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		
		
		//кран шаровой на спускник 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['1'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['2'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран под манометр с краном Маевского
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_manometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('4' => '15',
									   '69' => 'Кран',		
									   '156' =>  'G1/2 - М20х15');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '6');
		//Термометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['1'],		
									   '158' =>  $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['2'],		
									   '158' =>  $this->result['termometr']['dlina']['2']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Оправа к термометру 1-2 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//Бобышка к термометру
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Бобышка',
									   '156' => 'М27х2',
									   '192' => '25');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//гильза и бобышка к КТПТР-01
		if($this->project_quipment['preobDav']['select'] == '1')
		{
			//Гильза к КТПТР-01
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['all']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');			
			//Бобышка к КТПТР-01
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['all'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
		}
		//резьба под спускник 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		
		if($this->tso == '1')
		{
			//резьба под манометр
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['rezba']['mid'];
			$parametr['category'] = '336';
			$parametr['attribute'] = array('4' => '15',
										   '53' => '50',								   
										   '108' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '5');
		}
		
		if($this->tso == '2')
		{
			//отвод под манометр
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['otvod']['mid'];
			$parametr['category'] = '332';
			$parametr['attribute'] = array('4' => '15',
										   '10' => 'Резьба/Сварка',								   
										   '108' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		}
		
		
		//поросячие хвостики
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['perkensa']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Отвод под манометр',
									   '247' => 'Угловое');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		//подбор переходов
		$flag_flanec_vor = false;
		$count_flanc = 8;
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->project_dn['all']['dn']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc = 6;
			}
		}
		if($this->tso == '1')
		{
			$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->result['rashodomer']['all']['check_attribute']['4']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
				}
			}
			$perehod = $this->perehod($this->project_dn['all']['dn'], $this->result['rashodomer']['all']['check_attribute']['4']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{		
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
				}	
			}
		}
		//труба
		if($this->tso == '1')
		{
			if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['ddt'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['ddt']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');			
				if($this->project_dn['all']['dn'] < '50')
				{
					$i++;
					$parametr = array();
					$parametr['category'] = '364';
					$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
												   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
												   '227' => 'Без покрытия');									   
					$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
					
				}
				else
				{
					$i++;
					$parametr = array();
					$parametr['category'] = '519';
					$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
												   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
												   '227' => 'Без покрытия');									   
					$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
				}
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2,7');	
			}
			
			//бесшовная труба (прямой участок)
			$i++;
			$parametr = array();
			$parametr['category'] = '339';
			$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
										   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['all']['check_attribute']['4']],							   
										   '144' => '8732-78');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['all']['check_luch']*2/1000, 2));
		}
		
		if($this->tso == '2')
		{
			//бесшовная труба 
			if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
			{
				//бесшовная труба ДДТ
				$i++;
				$parametr = array();
				$parametr['category'] = '339';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['ddt']],						   
											   '144' => '8732-78');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');
				//бесшовная труба DN
				$i++;
				$parametr = array();
				$parametr['category'] = '339';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],						   
											   '144' => '8732-78');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');
			}
			else
			{
				//бесшовная труба DN и ДДТ
				$i++;
				$parametr = array();
				$parametr['category'] = '339';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],						   
											   '144' => '8732-78');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2,7');
			}
		}
		
		
		if($this->tso == '1')
		{
			//фланец воротниковый если есть переход
			if($flag_flanec_vor)
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '340';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '40' => '16',								   
											   '111' => 'Воротниковый');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
			}
			//фланец на краны
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '40' => '16',								   
										   '111' => 'Плоский');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), $count_flanc);
		}
		if($this->tso == '1')
		{	
			//фланец на расходомер
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
										   '40' => '16',								   
										   '111' => 'Плоский');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		if($this->tso == '1')
		{
			//прокладка паронитовая на краны
			$i++;
			$parametr = array();
			$parametr['category'] = '335';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '8');
		}
		if($this->tso == '1')
		{
			//прокладка паронитовая на расходомер
			$i++;
			$parametr = array();
			$parametr['category'] = '335';
			$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		//грунтовка
		$i++;
		$parametr = array();
		$parametr['id'] = '46270';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//краска
		$i++;
		$parametr = array();
		$parametr['id'] = '36278';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		return $order_data_spec_tm;
	}
	
	public function getSpecificationTMScheme2()
	{
		global $core, $db;
		$order_data_spec_tm = array();
		$i = 0;
		$dd_title ='';
		$order_data_spec_tm[] =  array(
			'tm_n'        		=> '',
			'tm_title' 			=> 'Теплосчётчик Т34М в составе: поз. 1-6',			
			'tm_model' 			=> '',			
			'tm_sku' 			=> '',			
			'tm_manufacter' 	=> 'ТЕРМОТРОНИК',			
			'tm_unit' 			=> '',			
			'tm_count' 			=> '',			
			'tm_weight' 		=> '',			
		);
		//вычислитель
		$i++;
		$parametr = array();
		$parametr['id'] = $this->project_quipment['preborRashod']['v_id']['2'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//расходомер
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['all']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//расходомер
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['94']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//датчик температуры
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['all'],
									   '241' => 'Комплект',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на подаче
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['1']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на обратке
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['2']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//кран шаровой
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '10' =>  'Фланцевое');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		if($this->project_dn['94']['dn'] > 55)
		{
			//затвор 
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['zatvor']['mid'];
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $this->project_dn['94']['dn']);		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		else
		{
			//кран
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['kran_muf']['mid'];
			$parametr['category'] = '286';
			$parametr['attribute'] = array('4' => $this->project_dn['94']['dn'],
										   '10' =>  'Муфтовое',		
										   '195' =>  'ручное (рычаг)',		
										   '244' =>  'ВР/ВР',		
										   '245' =>  'Нет');		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		//кран шаровой на спускник Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран шаровой на спускник Т94
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['94'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Манометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['1'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['2'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//кран под манометр с краном Маевского
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_manometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('4' => '15',
									   '69' => 'Кран',		
									   '156' =>  'G1/2 - М20х15');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '10');
		//Термометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['1'],		
									   '158' =>  $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['2'],		
									   '158' =>  $this->result['termometr']['dlina']['2']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Оправа к термометру 1-2 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//Бобышка к термометру
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Бобышка',
									   '156' => 'М27х2',
									   '192' => '25');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//гильза и бобышка к КТПТР-01
		if($this->project_quipment['preobDav']['select'] == '1')
		{
			//Гильза к КТПТР-01
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['all']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');			
			//Бобышка к КТПТР-01
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['all'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
		}
		//резьба под спускник Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
		//резьба под спускник Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['94'],
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//резьба под манометр
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => '15',
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '7');
		//поросячие хвостики
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['perkensa']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Отвод под манометр',
									   '247' => 'Угловое');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		//подбор переходов Т1-2
		$flag_flanec_vor = false;
		$count_flanc = 8;
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->project_dn['all']['dn']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}
		}
		$perehod = $this->perehod($this->project_dn['all']['dn'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}	
		}		
		//подбор переходов Т94
		$flag_flanec_vor_94 = false;
		$count_flanc_94 = 8;
		if($this->project_dn['94']['ddt'] > $this->project_dn['94']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['94']['ddt'], $this->project_dn['94']['dn']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');
					
				} 
				$flag_flanec_vor_94 = true;
				$count_flanc_94 = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['94']['ddt'], $this->result['rashodomer']['94']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}
		}
		$perehod = $this->perehod($this->project_dn['94']['dn'], $this->result['rashodomer']['94']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}	
		}	
		//труба Т1-2
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['ddt']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');			
			if($this->project_dn['all']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2,7');	
		}
		//труба Т94
		if($this->project_dn['94']['ddt'] > $this->project_dn['94']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['94']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['94']['ddt']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');			
			if($this->project_dn['94']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['94']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['94']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['94']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['94']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['94']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['94']['dn']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		}
		//бесшовная труба Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['all']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['all']['check_luch']*2/1000, 2));
		//бесшовная труба Т94
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['94']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['94']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['94']['check_luch']/1000, 2));
		//фланец воротниковый если есть переход
		if($flag_flanec_vor)
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		//фланец воротниковый Т94
		if($this->project_dn['94']['dn'] == '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['94']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}		
		//фланец на краны
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), $count_flanc);
		//фланец на расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//фланец на расходомер Т94
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['94']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//прокладка паронитовая на краны
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '8');
		//прокладка паронитовая на расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//прокладка паронитовая на расходомер Т94
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['94']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//грунтовка
		$i++;
		$parametr = array();
		$parametr['id'] = '46270';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');
		//краска
		$i++;
		$parametr = array();
		$parametr['id'] = '36278';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');		
		return $order_data_spec_tm;
	}
	
	public function getSpecificationTMScheme3()
	{
		global $core, $db;
		$order_data_spec_tm = array();
		$i = 0;
		$dd_title ='';
		$order_data_spec_tm[] =  array(
			'tm_n'        		=> '',
			'tm_title' 			=> 'Теплосчётчик Т34М в составе: поз. 1-8',			
			'tm_model' 			=> '',			
			'tm_sku' 			=> '',			
			'tm_manufacter' 	=> 'ТЕРМОТРОНИК',			
			'tm_unit' 			=> '',			
			'tm_count' 			=> '',			
			'tm_weight' 		=> '',			
		);
		//вычислитель
		$i++;
		$parametr = array();
		$parametr['id'] = $this->project_quipment['preborRashod']['v_id']['2'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['all']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//расходомер Т3
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['3']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//датчик температуры  Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['all'],
									   '241' => 'Комплект',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик температуры  Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['3'],
									   '241' => 'Одиночный',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//датчик давления на подаче
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['1']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на обратке
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['2']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления подаче ГВС
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['3']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//кран шаровой Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '10' =>  'Фланцевое');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');		
		//кран шаровой или затвор на Т3
		if($this->project_dn['3']['dn'] > 48)
		{
			//затвор 
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['zatvor']['mid'];
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn']);		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		else
		{
			//кран
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['kran_muf']['mid'];
			$parametr['category'] = '286';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '10' =>  'Муфтовое',		
										   '195' =>  'ручное (рычаг)',		
										   '244' =>  'ВР/ВР',		
										   '245' =>  'Нет');		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		//кран шаровой на спускник T1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран шаровой на спускник T3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['3'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Манометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['1'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['2'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['3'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран под манометр с краном Маевского
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_manometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('4' => '15',
									   '69' => 'Кран',		
									   '156' =>  'G1/2 - М20х15');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '12');
		//Термометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['1'],		
									   '158' =>  $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['2'],		
									   '158' =>  $this->result['termometr']['dlina']['2']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['3'],		
									   '158' =>  $this->result['termometr']['dlina']['3']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Оправа к термометру 1-2 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//Оправа к термометру 3 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['3']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//Бобышка к термометру
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Бобышка',
									   '156' => 'М27х2',
									   '192' => '25');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		//гильза и бобышка к КТПТР-01
		if($this->project_quipment['preobDav']['select'] == '1')
		{
			//Гильза к КТПТР-01 T1-2
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['all']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
			//Гильза к КТПТР-01 Т3
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['3']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
			//Бобышка к КТПТР-01 T1-2
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['all'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');					
			//Бобышка к КТПТР-01 Т3
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['3'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
			
		}
		//резьба под спускник Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
		//резьба под спускник Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['3'],
									   '53' => '50',								   
									   '108' => 'Оцинкованный');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//резьба под манометр Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => '15',
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '5');	
		//резьба под манометр Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => '15',
									   '53' => '50',								   
									   '108' => 'Оцинкованный');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//поросячие хвостики
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['perkensa']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Отвод под манометр',
									   '247' => 'Угловое');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		//подбор переходов Т1-2
		$flag_flanec_vor = false;
		$count_flanc = 8;
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->project_dn['all']['dn']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}
		}
		$perehod = $this->perehod($this->project_dn['all']['dn'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}	
		}	
		//подбор переходов Т3
		$flag_flanec_vor_3 = false;
		$count_flanc_3 = 8;
		if($this->project_dn['3']['ddt'] > $this->project_dn['3']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['3']['ddt'], $this->project_dn['3']['dn'], 'Оцинкованный');
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc_3 = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['3']['ddt'], $this->result['rashodomer']['3']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}
		}
		$perehod = $this->perehod($this->project_dn['3']['dn'], $this->result['rashodomer']['3']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}	
		}		
		//труба Т1-2
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['ddt']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');			
			if($this->project_dn['all']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2,7');	
		}
		//труба Т3
		if($this->project_dn['3']['ddt'] > $this->project_dn['3']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['3']['ddt']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,8');			
			if($this->project_dn['3']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,4');	
		}
		//бесшовная труба Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['all']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['all']['check_luch']*2/1000, 2));
		//бесшовная труба Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['3']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['3']['check_luch']/1000, 2));
		//фланец воротниковый если есть переход на Т1-2
		if($flag_flanec_vor)
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}		
		//фланец воротниковый Т3
		if($this->project_dn['3']['dn'] == '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}		
		//фланец на краны Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), $count_flanc);
		//фланец на краны Т3
		if($this->project_dn['3']['dn'] > 48 && $this->project_dn['3']['dn'] != '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '40' => '16',								   
										   '111' => 'Плоский');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		//фланец на расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//фланец на расходомер Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//прокладка паронитовая на краны
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '8');
		//прокладка паронитовая на расходомеры Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//прокладка паронитовая на расходомер Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//грунтовка
		$i++;
		$parametr = array();
		$parametr['id'] = '46270';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');
		//краска
		$i++;
		$parametr = array();
		$parametr['id'] = '36278';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');		
		return $order_data_spec_tm;
	}
	
	public function getSpecificationTMScheme4()
	{
		global $core, $db;
		$order_data_spec_tm = array();
		$i = 0;
		$dd_title ='';
		$order_data_spec_tm[] =  array(
			'tm_n'        		=> '',
			'tm_title' 			=> 'Теплосчётчик Т34М в составе: поз. 1-5',			
			'tm_model' 			=> '',			
			'tm_sku' 			=> '',			
			'tm_manufacter' 	=> 'ТЕРМОТРОНИК',			
			'tm_unit' 			=> '',			
			'tm_count' 			=> '',			
			'tm_weight' 		=> '',			
		);
		//вычислитель
		$i++;
		$parametr = array();
		$parametr['id'] = $this->project_quipment['preborRashod']['v_id']['2'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['all']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//расходомер Т3
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['3']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//расходомер Т4
		$i++;
		$parametr = array();
		$parametr['id'] = $this->result['rashodomer']['4']['check_id'];
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//датчик температуры  Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['all'],
									   '241' => 'Комплект',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик температуры  Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobTeplo']['mid'];
		$parametr['category'] = '318';
		$parametr['attribute'] = array('42' => $this->result['ldt']['3'],
									   '241' => 'Комплект',
									   '36' =>'Pt100');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');		
		//датчик давления на подаче
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['1']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления на обратке
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['2']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления подаче ГВС
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['3']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//датчик давления циркуляции ГВС
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['preobDav']['mid'];
		$parametr['category'] = '280';
		$parametr['attribute'] = array('178' => $this->commasReplace($this->result['pdd']['4']),
									   '242' =>  $this->project_quipment['preobDav']['predel']);		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//кран шаровой Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '10' =>  'Фланцевое');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');		
		//кран шаровой или затвор на Т3
		if($this->project_dn['3']['dn'] > 48)
		{
			//затвор 
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['zatvor']['mid'];
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn']);		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		else
		{
			//кран
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['kran_muf']['mid'];
			$parametr['category'] = '286';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '10' =>  'Муфтовое',		
										   '195' =>  'ручное (рычаг)',		
										   '244' =>  'ВР/ВР',		
										   '245' =>  'Нет');		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		//кран шаровой или затвор на Т4
		if($this->project_dn['4']['dn'] > 48)
		{
			//затвор 
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['zatvor']['mid'];
			$parametr['category'] = '288';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['dn']);		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		else
		{
			//кран
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['kran_muf']['mid'];
			$parametr['category'] = '286';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
										   '10' =>  'Муфтовое',		
										   '195' =>  'ручное (рычаг)',		
										   '244' =>  'ВР/ВР',		
										   '245' =>  'Нет');		
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}
		//кран шаровой на спускник T1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран шаровой на спускник T3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['3'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//кран шаровой на спускник T4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_sp']['mid'];
		$parametr['category'] = '286';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['4'],
									   '10' =>  'Муфтовое',		
									   '195' =>  'ручное (рычаг)',		
									   '244' =>  'ВР/ВР',		
									   '245' =>  'Нет');		
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Манометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['1'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['2'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['3'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Манометр 4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['manometr']['mid'];
		$parametr['category'] = '297';
		$parametr['attribute'] = array('58' => 'Стандартное исполнение',
									   '155' =>  $this->result['manometr']['4'],		
									   '156' =>  'М20×1.5');	
		if($this->project_quipment['manometr']['mid'] == '18')
		{
			$parametr['attribute']['246'] = 'Модель 2 (М2)';
		}							   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//кран под манометр с краном Маевского
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['kran_manometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('4' => '15',
									   '69' => 'Кран',		
									   '156' =>  'G1/2 - М20х15');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '16');
		//Термометр 1
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['1'],		
									   '158' =>  $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['2'],		
									   '158' =>  $this->result['termometr']['dlina']['2']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');
		//Термометр 3-4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '298';
		$parametr['attribute'] = array('62' => 'Жидкостный',
									   '89' => $this->result['termometr']['range']['3'],		
									   '158' =>  $this->result['termometr']['dlina']['3']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//Оправа к термометру 1-2 
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['1']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//Оправа к термометру 3-4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Оправа к термометру',
									   '192' => $this->result['termometr']['dlina']['3']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		//Бобышка к термометру
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['termometr']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Бобышка',
									   '156' => 'М27х2',
									   '192' => '25');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//гильза и бобышка к КТПТР-01
		if($this->project_quipment['preobDav']['select'] == '1')
		{
			//Гильза к КТПТР-01 T1-2
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['all']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
			//Гильза к КТПТР-01 Т3-4
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['gilza']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Гильза',
										   '192' => $this->result['ldt']['3']);									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
			//Бобышка к КТПТР-01 T1-2
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['all'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');					
			//Бобышка к КТПТР-01 Т3-4
			$i++;
			$parametr = array();
			$parametr['man'] = $this->project_quipment['bob']['mid'];
			$parametr['category'] = '299';
			$parametr['attribute'] = array('69' => 'Бобышка',
										   '192' => $this->result['lbob_spec']['3'],								   
										   '156' => 'М20×1.5');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
			
		}
		//резьба под спускник Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['all'],
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');	
		//резьба под спускник Т3
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['3'],
									   '53' => '50',								   
									   '108' => 'Оцинкованный');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//резьба под спускник Т4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => $this->result['dn_sp']['4'],
									   '53' => '50',								   
									   '108' => 'Оцинкованный');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1');	
		//резьба под манометр Т1-2
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => '15',
									   '53' => '50',								   
									   '108' => 'Без покрытия');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '5');	
		//резьба под манометр Т3-4
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['rezba']['mid'];
		$parametr['category'] = '336';
		$parametr['attribute'] = array('4' => '15',
									   '53' => '50',								   
									   '108' => 'Оцинкованный');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '8');
		//поросячие хвостики
		$i++;
		$parametr = array();
		$parametr['man'] = $this->project_quipment['perkensa']['mid'];
		$parametr['category'] = '299';
		$parametr['attribute'] = array('69' => 'Отвод под манометр',
									   '247' => 'Угловое');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '3');
		//подбор переходов Т1-2
		$flag_flanec_vor = false;
		$count_flanc = 8;
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->project_dn['all']['dn']);
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['all']['ddt'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}
		}
		$perehod = $this->perehod($this->project_dn['all']['dn'], $this->result['rashodomer']['all']['check_attribute']['4']);
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '2');		
			}	
		}	
		//подбор переходов Т3
		$flag_flanec_vor_3 = false;
		$count_flanc_3 = 8;
		if($this->project_dn['3']['ddt'] > $this->project_dn['3']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['3']['ddt'], $this->project_dn['3']['dn'], 'Оцинкованный');
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc_3 = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['3']['ddt'], $this->result['rashodomer']['3']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}
		}
		$perehod = $this->perehod($this->project_dn['3']['dn'], $this->result['rashodomer']['3']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}	
		}	
		//подбор переходов Т4
		$flag_flanec_vor_3 = false;
		$count_flanc_3 = 8;
		if($this->project_dn['4']['ddt'] > $this->project_dn['4']['dn'])
		{	
			$perehod = $this->perehod($this->project_dn['4']['ddt'], $this->project_dn['4']['dn'], 'Оцинкованный');
			if(isset($perehod))
			{
				foreach ($perehod as $key => $value) 
				{
					$i++;
					$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');
					
				} 
				$flag_flanec_vor = true;
				$count_flanc_3 = 6;
			}
		}	
		$perehod = $this->perehod($this->project_dn['4']['ddt'], $this->result['rashodomer']['4']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}
		}
		$perehod = $this->perehod($this->project_dn['4']['dn'], $this->result['rashodomer']['4']['check_attribute']['4'], 'Оцинкованный');
		if(isset($perehod))
		{
			foreach ($perehod as $key => $value) 
			{		
				$i++;
				$order_data_spec_tm[] = $this->structureSpecification($i, $value, '1');		
			}	
		}		
		//труба Т1-2
		if($this->project_dn['all']['ddt'] > $this->project_dn['all']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['ddt']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,5');			
			if($this->project_dn['all']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
											   '227' => 'Без покрытия');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,2');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['all']['dn']],								   
										   '227' => 'Без покрытия');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2,7');	
		}
		//труба Т3
		if($this->project_dn['3']['ddt'] > $this->project_dn['3']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['3']['ddt']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,8');			
			if($this->project_dn['3']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['3']['dn']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,4');	
		}
		//труба Т4
		if($this->project_dn['4']['ddt'] > $this->project_dn['4']['dn'])
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['ddt'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['4']['ddt']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,8');			
			if($this->project_dn['4']['dn'] < '50')
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '364';
				$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['4']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
				
			}
			else
			{
				$i++;
				$parametr = array();
				$parametr['category'] = '519';
				$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
											   '110' => $this->sprav['tolshina'][$this->project_dn['4']['dn']],								   
											   '227' => 'Оцинкованная');									   
				$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '0,6');	
			}
		}
		else
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '519';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
										   '110' => $this->sprav['tolshina'][$this->project_dn['4']['dn']],								   
										   '227' => 'Оцинкованная');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '1,4');	
		}
		//бесшовная труба Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['all']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['all']['check_luch']*2/1000, 2));
		//бесшовная труба Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['3']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['3']['check_luch']/1000, 2));
		//бесшовная труба Т4
		$i++;
		$parametr = array();
		$parametr['category'] = '339';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['4']['check_attribute']['4'],
									   '110' => $this->sprav['tolshina_bezshov'][$this->result['rashodomer']['4']['check_attribute']['4']],								   
									   '144' => '8732-78');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), round($this->result['rashodomer']['4']['check_luch']/1000, 2));
		//фланец воротниковый если есть переход на Т1-2
		if($flag_flanec_vor)
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		}		
		//фланец воротниковый Т3
		if($this->project_dn['3']['dn'] == '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}		
		//фланец воротниковый Т4
		if($this->project_dn['4']['dn'] == '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
										   '40' => '16',								   
										   '111' => 'Воротниковый');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}		
		//фланец на краны Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), $count_flanc);
		//фланец на краны Т3
		if($this->project_dn['3']['dn'] > 48 && $this->project_dn['3']['dn'] != '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['3']['dn'],
										   '40' => '16',								   
										   '111' => 'Плоский');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		//фланец на краны Т4
		if($this->project_dn['4']['dn'] > 48 && $this->project_dn['4']['dn'] != '65')
		{
			$i++;
			$parametr = array();
			$parametr['category'] = '340';
			$parametr['attribute'] = array('4' => $this->project_dn['4']['dn'],
										   '40' => '16',								   
										   '111' => 'Плоский');									   
			$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		}
		//фланец на расходомер Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//фланец на расходомер Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//фланец на расходомер Т4
		$i++;
		$parametr = array();
		$parametr['category'] = '340';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['4']['check_attribute']['4'],
									   '40' => '16',								   
									   '111' => 'Плоский');									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//прокладка паронитовая на краны
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->project_dn['all']['dn']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '8');
		//прокладка паронитовая на расходомеры Т1-2
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['all']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '4');
		//прокладка паронитовая на расходомер Т3
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['3']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//прокладка паронитовая на расходомер Т4
		$i++;
		$parametr = array();
		$parametr['category'] = '335';
		$parametr['attribute'] = array('4' => $this->result['rashodomer']['4']['check_attribute']['4']);									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//грунтовка
		$i++;
		$parametr = array();
		$parametr['id'] = '46270';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');
		//краска
		$i++;
		$parametr = array();
		$parametr['id'] = '36278';									   
		$order_data_spec_tm[] = $this->structureSpecification($i, $this->getCatalog($parametr), '2');		
		return $order_data_spec_tm;
	}
	
	public function getSpecificationATM()
	{
		global $core, $db;
		$order_data_spec_atm = array();
		$query_spec_atm = $db->query("SELECT s.*,c.sku, c.unit, c.title, c.spec, c.weight, u.unit, u.sname as unit_sname, m.name as manufacture_name FROM ".DB_PREFIX."_metering_scheme_detail as s LEFT JOIN  ".DB_PREFIX."_catalog as c ON(c.id = s.cid) LEFT JOIN ".DB_PREFIX."_catalog_unit as u on(u.unit=c.unit) LEFT JOIN ".DB_PREFIX."_catalog_manufacture as m on(m.id = c.man) WHERE s.scheme_id = '".$this->scheme."' ORDER BY s.position, s.id ASC");		
		if($db->numRows($query_spec_atm) > 0) 
		{
			$i = 0;
			$total_all = 0;
			while($catalog = $db->getRow($query_spec_atm)) 
			{
				$i++;
				$sku = $catalog['sku'];
				$unit = $catalog['unit_sname'];
				$name = $catalog['title'];
				$count = $catalog['count'];
				$model = ' ';
				$manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
				$spec_array = json_decode($catalog['spec'], true);
				$weight	 = json_decode($catalog['weight'], true);
				if (!empty($spec_array['1']))
				{
					$name = $spec_array['1'];
				}
				if (!empty($spec_array['2']))
				{
					$model = str_replace("&lt;br&gt;", "<w:br/>", $spec_array['2']);
				}
				if (!empty($spec_array['3']))
				{
					$manufacter = $spec_array['3'];
				}
				$weight_good = '';
				if (!empty($weight['1']))
				{
					$weight_good = $weight['1'];
				}
				$order_data_spec_atm[] =  array(
					'atm_n'        			=> $i,
					'atm_title' 			=> $name,			
					'atm_model' 			=> $model,			
					'atm_sku' 				=> $sku,			
					'atm_manufacter' 		=> $manufacter,			
					'atm_unit' 				=> $unit,			
					'atm_count' 			=> number_format($count,1,',',''),			
					'atm_weight' 			=> $weight_good,			
				);
			}
		}
		return $order_data_spec_atm;
	}
	
	
	
	public function printOut()
	{
		
		$tpl = ROOT.'/files/tpl/metering/tso/'.$this->tso.'/scheme_'.$this->scheme.'_pz.docx';
		if(!file_exists($tpl))
		{
			echo 'Шаблон проекта не обнаружен, попробуйте зайти позже;';
			exit;
		}
		$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);		
		$file_url = ROOT.'files/metering/output/print_pz.docx';
		$file_url_pdf = ROOT.'files/metering/output/print_pz.pdf';
		
		//информация о проекте
		$document->setValue('pr_numb', $this->project_info['pr_numb']);      
		$document->setValue('tu_numb', $this->project_info['tu_numb']);      
		$document->setValue('tu_date', $this->project_info['tu_date']);   
		$document->setValue('dog_numb', $this->project_info['dog_numb']);      
		$document->setValue('dog_date', $this->project_info['dog_date']); 
		$document->setValue('org_name', $this->project_info['org_name']);      
		$document->setValue('object_name', $this->project_info['object_name']);      
		$document->setValue('object_address', $this->project_info['object_address']);      
		$document->setValue('sys_list_str', $this->project_system['sys_list_str']);      
		//информация о параметрах
		$document->setValue('t1', $this->project_parameter['t1']);      
		$document->setValue('t2', $this->project_parameter['t2']);      
		$document->setValue('t3', $this->project_parameter['t3']);      
		$document->setValue('t4', $this->project_parameter['t4']);  
		$document->setValue('p1', $this->project_parameter['p1']);  
		$document->setValue('p2', $this->project_parameter['p2']);  
		$document->setValue('p3', $this->project_parameter['p3']);  
		$document->setValue('p4', $this->project_parameter['p4']);  			  
		$document->setValue('p110', $this->result['p10']['1']);      
		$document->setValue('p210', $this->result['p10']['2']); 
		$document->setValue('p310', $this->result['p10']['3']); 
		$document->setValue('p410', $this->result['p10']['4']); 
		//информация об основном оборудовании		
		$document->setValue('preobTeplo_title', $this->project_quipment['preobTeplo']['title']);   
		$document->setValue('preobTeplo_man', $this->project_quipment['preobTeplo']['man']); 
		$document->setValue('preobDav_title', $this->project_quipment['preobDav']['title']);   
		$document->setValue('preobDav_man', $this->project_quipment['preobDav']['man']);  
		$document->setValue('dt_deltat', $this->project_quipment['preobTeplo']['deltat']);  
		$document->setValue('dt_klass_1', $this->project_quipment['preobTeplo']['klass']['1']);  
		$document->setValue('dt_klass_2', $this->project_quipment['preobTeplo']['klass']['2']);  
		$document->setValue('dd_marka_1', $this->project_quipment['preobDav']['marka']['1']);  
		$document->setValue('dd_marka_2', $this->project_quipment['preobDav']['marka']['2']);  
		$document->setValue('dd_marka_3', $this->project_quipment['preobDav']['marka']['3']);  
		$document->setValue('dd_marka_4', $this->project_quipment['preobDav']['marka']['4']);  
		//расход воды
		$document->setValue('q_gvs', $this->project_system['gvs']['1']['q']); 
		//расход энергии
		$document->setValue('g_all', $this->project_system['g']['all']);  
		$document->setValue('g_3', $this->project_system['g']['3']);  
		$document->setValue('g_4', $this->project_system['g']['4']);  
		$document->setValue('g_94', $this->project_system['g']['94']);		
		//типоразмер датчика давления
		$document->setValue('d_all', $this->resultOut(array('rashodomer','all','check_attribute','4')));
		$document->setValue('d_3', $this->resultOut(array('rashodomer','3','check_attribute','4')));
		$document->setValue('d_4', $this->resultOut(array('rashodomer','4','check_attribute','4')));		
		$document->setValue('d_94', $this->resultOut(array('rashodomer','94','check_attribute','4')));		
		//подбор расходомеров
		$document->setValue('rashodomer_all', $this->resultOut(array('rashodomer','all','check_spec','2')));
		$document->setValue('rashodomer_3', $this->resultOut(array('rashodomer','3','check_spec','2')));
		$document->setValue('rashodomer_4', $this->resultOut(array('rashodomer','4','check_spec','2')));
		$document->setValue('rashodomer_94', $this->resultOut(array('rashodomer','94','check_spec','2')));
		//располагаемый напор
		$document->setValue('dp_all', $this->result['dp']['all']);
		$document->setValue('dp_3', $this->result['dp']['3']);
		$document->setValue('dp_4', $this->result['dp']['4']);
		$document->setValue('dp_94', $this->result['dp']['94']);
		//диапазон измерений
		$document->setValue('g_max_all', $this->resultOut(array('rashodomer','all','check_attribute','189')));		
		$document->setValue('g_max_3', $this->resultOut(array('rashodomer','3','check_attribute','189')));		
		$document->setValue('g_max_4', $this->resultOut(array('rashodomer','4','check_attribute','189')));		
		$document->setValue('g_max_94', $this->resultOut(array('rashodomer','94','check_attribute','189')));		
		$document->setValue('g_min_all', $this->resultOut(array('rashodomer','all','check_attribute','187')));		
		$document->setValue('g_min_3', $this->resultOut(array('rashodomer','3','check_attribute','187')));		
		$document->setValue('g_min_4', $this->resultOut(array('rashodomer','4','check_attribute','187')));		
		$document->setValue('g_min_94', $this->resultOut(array('rashodomer','94','check_attribute','187')));		
		//снижение напора на узле
		$document->setValue('sum_dp_all', round($this->result['sum_dp']['all'],2));
		$document->setValue('sum_dp_3', round($this->result['sum_dp']['3'],2));
		$document->setValue('sum_dp_4', round($this->result['sum_dp']['4'],2));
		$document->setValue('sum_dp_94',  round($this->result['sum_dp']['94'],2));		
		//наименование расходомера на все 4 
		$document->setValue('rashodomer_excel_1', $this->resultOut(array('rashodomer','all','check_parametr','rashodomer_excel','1'))); 
		$document->setValue('rashodomer_excel_2', $this->resultOut(array('rashodomer','all','check_parametr','rashodomer_excel','2'))); 
		$document->setValue('rashodomer_excel_3', $this->resultOut(array('rashodomer','3','check_parametr','rashodomer_excel','1'))); 
		$document->setValue('rashodomer_excel_4', $this->resultOut(array('rashodomer','4','check_parametr','rashodomer_excel','1'))); 
		$document->setValue('rashodomer_excel_94', $this->resultOut(array('rashodomer','94','check_parametr','rashodomer_excel','1'))); 
		//конфузор на 4 расходомера
		$document->setValue('konfuz_1', $this->resultOut(array('rashodomer','all','check_parametr','konfuz','1')));  
		$document->setValue('konfuz_2', $this->resultOut(array('rashodomer','all','check_parametr','konfuz','2')));
		$document->setValue('konfuz_3', $this->resultOut(array('rashodomer','3','check_parametr','konfuz','1')));  
		$document->setValue('konfuz_4', $this->resultOut(array('rashodomer','4','check_parametr','konfuz','2')));
		$document->setValue('konfuz_94', $this->resultOut(array('rashodomer','94','check_parametr','konfuz','2')));
		//диффузор на 4 расходомера
		$document->setValue('diffuz_1', $this->resultOut(array('rashodomer','all','check_parametr','diffuz','1')));  
		$document->setValue('diffuz_2', $this->resultOut(array('rashodomer','all','check_parametr','diffuz','2')));  
		$document->setValue('diffuz_3', $this->resultOut(array('rashodomer','3','check_parametr','diffuz','1')));  
		$document->setValue('diffuz_4', $this->resultOut(array('rashodomer','4','check_parametr','diffuz','2')));
		$document->setValue('diffuz_94', $this->resultOut(array('rashodomer','94','check_parametr','diffuz','2')));
		//Длина сужения на 4 расходомера
		$document->setValue('luch_1', $this->resultOut(array('rashodomer','all','check_luch')));	
		$document->setValue('luch_2', $this->resultOut(array('rashodomer','all','check_luch')));	
		$document->setValue('luch_3', $this->resultOut(array('rashodomer','3','check_luch')));	
		$document->setValue('luch_4', $this->resultOut(array('rashodomer','4','check_luch')));	
		$document->setValue('luch_94', $this->resultOut(array('rashodomer','94','check_luch')));	
		//диаметр сужения на 4 расходомера
		$document->setValue('excel_d0_1', $this->resultOut(array('rashodomer','all','check_excel', 'd0_1')));	
		$document->setValue('excel_d0_2', $this->resultOut(array('rashodomer','all','check_excel', 'd0_2')));	
		$document->setValue('excel_d0_3', $this->resultOut(array('rashodomer','3','check_excel', 'd0_1')));	
		$document->setValue('excel_d0_4', $this->resultOut(array('rashodomer','4','check_excel', 'd0_1')));	
		$document->setValue('excel_d0_94', $this->resultOut(array('rashodomer','94','check_excel', 'd0_1')));	
		//диаметр трубопровода перед конфузором на 4 расходомера
		$document->setValue('excel_d1_1', $this->resultOut(array('rashodomer','all','check_excel', 'd1_1')));	
		$document->setValue('excel_d1_2', $this->resultOut(array('rashodomer','all','check_excel', 'd1_2')));	
		$document->setValue('excel_d1_3', $this->resultOut(array('rashodomer','3','check_excel', 'd1_1')));	
		$document->setValue('excel_d1_4', $this->resultOut(array('rashodomer','4','check_excel', 'd1_1')));			
		$document->setValue('excel_d1_94', $this->resultOut(array('rashodomer','94','check_excel', 'd1_1')));			
		//диаметр трубопровода после диффузора на 4 расходомера
		$document->setValue('excel_d2_1', $this->resultOut(array('rashodomer','all','check_excel', 'd2_1')));	
		$document->setValue('excel_d2_2', $this->resultOut(array('rashodomer','all','check_excel', 'd2_2')));	
		$document->setValue('excel_d2_3', $this->resultOut(array('rashodomer','3','check_excel', 'd2_1')));	
		$document->setValue('excel_d2_4', $this->resultOut(array('rashodomer','4','check_excel', 'd2_1')));	
		$document->setValue('excel_d2_94', $this->resultOut(array('rashodomer','94','check_excel', 'd2_1')));	
		//угол раскрытия конфузора на 4 расходомера
		$document->setValue('excel_a1_1', $this->resultOut(array('rashodomer','all','check_excel', 'a1_1')));	
		$document->setValue('excel_a1_2', $this->resultOut(array('rashodomer','all','check_excel', 'a1_2')));	
		$document->setValue('excel_a1_3', $this->resultOut(array('rashodomer','3','check_excel', 'a1_1')));	
		$document->setValue('excel_a1_4', $this->resultOut(array('rashodomer','4','check_excel', 'a1_1')));	
		$document->setValue('excel_a1_94', $this->resultOut(array('rashodomer','94','check_excel', 'a1_1')));	
		//угол раскрытия диффузора на 4 расходомера
		$document->setValue('excel_a2_1', $this->resultOut(array('rashodomer','all','check_excel', 'a2_1')));	
		$document->setValue('excel_a2_2', $this->resultOut(array('rashodomer','all','check_excel', 'a2_2')));	
		$document->setValue('excel_a2_3', $this->resultOut(array('rashodomer','3','check_excel', 'a2_1')));	
		$document->setValue('excel_a2_4', $this->resultOut(array('rashodomer','4','check_excel', 'a2_1')));	
		$document->setValue('excel_a2_94', $this->resultOut(array('rashodomer','94','check_excel', 'a2_1')));	
		//плотность воды на 4 расходомера
		$document->setValue('excel_r_1', $this->resultOut(array('rashodomer','all','check_excel', 'r_1')));	
		$document->setValue('excel_r_2', $this->resultOut(array('rashodomer','all','check_excel', 'r_2')));	
		$document->setValue('excel_r_3', $this->resultOut(array('rashodomer','3','check_excel', 'r_1')));	
		$document->setValue('excel_r_4', $this->resultOut(array('rashodomer','4','check_excel', 'r_1')));	
		$document->setValue('excel_r_94', $this->resultOut(array('rashodomer','94','check_excel', 'r_1')));	
		//объемный расход воды на 4 расходомера
		$document->setValue('excel_q_1', $this->resultOut(array('rashodomer','all','check_excel', 'q_1')));	
		$document->setValue('excel_q_2', $this->resultOut(array('rashodomer','all','check_excel', 'q_2')));	
		$document->setValue('excel_q_3', $this->resultOut(array('rashodomer','3','check_excel', 'q_1')));	
		$document->setValue('excel_q_4', $this->resultOut(array('rashodomer','4','check_excel', 'q_1')));	
		$document->setValue('excel_q_94', $this->resultOut(array('rashodomer','94','check_excel', 'q_1')));	
		//скорость в сужении D0 на 4 расходомера
		$document->setValue('excel_v0_1', $this->resultOut(array('rashodomer','all','check_excel', 'v0_1')));	
		$document->setValue('excel_v0_2', $this->resultOut(array('rashodomer','all','check_excel', 'v0_2')));	
		$document->setValue('excel_v0_3', $this->resultOut(array('rashodomer','3','check_excel', 'v0_1')));	
		$document->setValue('excel_v0_4', $this->resultOut(array('rashodomer','4','check_excel', 'v0_1')));	
		$document->setValue('excel_v0_94', $this->resultOut(array('rashodomer','94','check_excel', 'v0_1')));	
		//скорость в сужении D1 на 4 расходомера
		$document->setValue('excel_v1_1', $this->resultOut(array('rashodomer','all','check_excel', 'v1_1')));	
		$document->setValue('excel_v1_2', $this->resultOut(array('rashodomer','all','check_excel', 'v1_2')));	
		$document->setValue('excel_v1_3', $this->resultOut(array('rashodomer','3','check_excel', 'v1_1')));	
		$document->setValue('excel_v1_4', $this->resultOut(array('rashodomer','4','check_excel', 'v1_1')));	
		$document->setValue('excel_v1_94', $this->resultOut(array('rashodomer','94','check_excel', 'v1_1')));	
		//скорость в сужении D2 на 4 расходомера
		$document->setValue('excel_v2_1', $this->resultOut(array('rashodomer','all','check_excel', 'v2_1')));	
		$document->setValue('excel_v2_2', $this->resultOut(array('rashodomer','all','check_excel', 'v2_2')));	
		$document->setValue('excel_v2_3', $this->resultOut(array('rashodomer','3','check_excel', 'v2_1')));	
		$document->setValue('excel_v2_4', $this->resultOut(array('rashodomer','4','check_excel', 'v2_1')));	
		$document->setValue('excel_v2_94', $this->resultOut(array('rashodomer','94','check_excel', 'v2_1')));	
		//коэффициент сопротивления трения на конфузоре на 4 расходомера
		$document->setValue('excel_xtr_1', $this->resultOut(array('rashodomer','all','check_excel', 'xtr_1')));	
		$document->setValue('excel_xtr_2', $this->resultOut(array('rashodomer','all','check_excel', 'xtr_2')));	
		$document->setValue('excel_xtr_3', $this->resultOut(array('rashodomer','3','check_excel', 'xtr_1')));	
		$document->setValue('excel_xtr_4', $this->resultOut(array('rashodomer','4','check_excel', 'xtr_1')));	
		$document->setValue('excel_xtr_94', $this->resultOut(array('rashodomer','94','check_excel', 'xtr_1')));	
		//потеря напора на конфузоре на 4 расходомера
		$document->setValue('excel_dhk_1', $this->resultOut(array('rashodomer','all','check_excel', 'dhk_1')));	
		$document->setValue('excel_dhk_2', $this->resultOut(array('rashodomer','all','check_excel', 'dhk_2')));	
		$document->setValue('excel_dhk_3', $this->resultOut(array('rashodomer','3','check_excel', 'dhk_1')));	
		$document->setValue('excel_dhk_4', $this->resultOut(array('rashodomer','4','check_excel', 'dhk_1')));
		$document->setValue('excel_dhk_94', $this->resultOut(array('rashodomer','94','check_excel', 'dhk_1')));
		//коэффициент гидравлического трения на 4 расходомера
		$document->setValue('excel_l_1', $this->resultOut(array('rashodomer','all','check_excel', 'l_1')));	
		$document->setValue('excel_l_2', $this->resultOut(array('rashodomer','all','check_excel', 'l_2')));	
		$document->setValue('excel_l_3', $this->resultOut(array('rashodomer','3','check_excel', 'l_1')));	
		$document->setValue('excel_l_4', $this->resultOut(array('rashodomer','4','check_excel', 'l_1')));
		$document->setValue('excel_l_94', $this->resultOut(array('rashodomer','94','check_excel', 'l_1')));
		//потери на прямом участке на 4 расходомера
		$document->setValue('excel_dhp_1', $this->resultOut(array('rashodomer','all','check_excel', 'dhp_1')));	
		$document->setValue('excel_dhp_2', $this->resultOut(array('rashodomer','all','check_excel', 'dhp_2')));	
		$document->setValue('excel_dhp_3', $this->resultOut(array('rashodomer','3','check_excel', 'dhp_1')));	
		$document->setValue('excel_dhp_4', $this->resultOut(array('rashodomer','4','check_excel', 'dhp_1')));
		$document->setValue('excel_dhp_94', $this->resultOut(array('rashodomer','94','check_excel', 'dhp_1')));
		//коэффициент сопротивления расширения на 4 расходомера
		$document->setValue('excel_xrp_1', $this->resultOut(array('rashodomer','all','check_excel', 'xrp_1')));	
		$document->setValue('excel_xrp_2', $this->resultOut(array('rashodomer','all','check_excel', 'xrp_2')));	
		$document->setValue('excel_xrp_3', $this->resultOut(array('rashodomer','3','check_excel', 'xrp_1')));	
		$document->setValue('excel_xrp_4', $this->resultOut(array('rashodomer','4','check_excel', 'xrp_1')));
		$document->setValue('excel_xrp_94', $this->resultOut(array('rashodomer','94','check_excel', 'xrp_1')));
		//коэффициент сопротивления трения на диффузоре на 4 расходомера
		$document->setValue('excel_xtrd_1', $this->resultOut(array('rashodomer','all','check_excel', 'xtrd_1')));	
		$document->setValue('excel_xtrd_2', $this->resultOut(array('rashodomer','all','check_excel', 'xtrd_2')));	
		$document->setValue('excel_xtrd_3', $this->resultOut(array('rashodomer','3','check_excel', 'xtrd_1')));	
		$document->setValue('excel_xtrd_4', $this->resultOut(array('rashodomer','4','check_excel', 'xtrd_1')));
		$document->setValue('excel_xtrd_94', $this->resultOut(array('rashodomer','94','check_excel', 'xtrd_1')));
		//потеря напора на диффузоре на 4 расходомера
		$document->setValue('excel_dhd_1', $this->resultOut(array('rashodomer','all','check_excel', 'dhd_1')));	
		$document->setValue('excel_dhd_2', $this->resultOut(array('rashodomer','all','check_excel', 'dhd_2')));	
		$document->setValue('excel_dhd_3', $this->resultOut(array('rashodomer','3','check_excel', 'dhd_1')));	
		$document->setValue('excel_dhd_4', $this->resultOut(array('rashodomer','4','check_excel', 'dhd_1')));
		$document->setValue('excel_dhd_94', $this->resultOut(array('rashodomer','94','check_excel', 'dhd_1')));
		//суммарная потеря напора в м.в.ст на 4 расходомера
		$document->setValue('excel_sum_1', $this->resultOut(array('rashodomer','all','check_excel', 'sum_1')));	
		$document->setValue('excel_sum_2', $this->resultOut(array('rashodomer','all','check_excel', 'sum_2')));	
		$document->setValue('excel_sum_3', $this->resultOut(array('rashodomer','3','check_excel', 'sum_1')));	
		$document->setValue('excel_sum_4', $this->resultOut(array('rashodomer','4','check_excel', 'sum_1')));
		$document->setValue('excel_sum_94', $this->resultOut(array('rashodomer','94','check_excel', 'sum_1')));
		//суммарная потеря напора в кгс/см2 на 4 расходомера
		$document->setValue('excel_total_1', $this->resultOut(array('rashodomer','all','check_excel', 'total_1')));	
		$document->setValue('excel_total_2', $this->resultOut(array('rashodomer','all','check_excel', 'total_2')));	
		$document->setValue('excel_total_3', $this->resultOut(array('rashodomer','3','check_excel', 'total_1')));	
		$document->setValue('excel_total_4', $this->resultOut(array('rashodomer','4','check_excel', 'total_1')));
		$document->setValue('excel_total_94', $this->resultOut(array('rashodomer','94','check_excel', 'total_1')));
		//диаметр основного трубопровода с единицей измерения 
		$document->setValue('dn_all', $this->project_dn['all']['dn_str']);  
		$document->setValue('dn_3', $this->project_dn['3']['dn_str']); 
		$document->setValue('dn_4', $this->project_dn['4']['dn_str']); 
		$document->setValue('dn_94', $this->project_dn['94']['dn_str']); 
		//диаметр трубопровода КИП
		$document->setValue('ddt_all', $this->project_dn['all']['ddt']);  
		$document->setValue('ddt_3', $this->project_dn['3']['ddt']); 
		$document->setValue('ddt_4', $this->project_dn['4']['ddt']); 
		$document->setValue('ddt_94', $this->project_dn['94']['ddt']); 
		//договорное давление для таблицы программирования
		$document->setValue('pdog_1', $this->result['pdog']['1']);
		$document->setValue('pdog_2', $this->result['pdog']['2']);
		$document->setValue('pdog_3', $this->result['pdog']['3']);
		$document->setValue('pdog_4', $this->result['pdog']['4']);	
		//верхний предел измерения датчика давления в килограмм силы
		$document->setValue('pdd_1_kgs', $this->result['pdd_kgs']['1']);
		$document->setValue('pdd_2_kgs', $this->result['pdd_kgs']['2']);
		$document->setValue('pdd_3_kgs', $this->result['pdd_kgs']['3']);
		$document->setValue('pdd_4_kgs', $this->result['pdd_kgs']['4']);	
		//верхний предел измерения датчика давления в МПа
		$document->setValue('pdd_1', $this->result['pdd']['1']);
		$document->setValue('pdd_2', $this->result['pdd']['2']);
		$document->setValue('pdd_3', $this->result['pdd']['3']);
		$document->setValue('pdd_4', $this->result['pdd']['4']);	
		//длину датчика температуры
		$document->setValue('ldt_all', $this->result['ldt']['all']);
		$document->setValue('ldt_3', $this->result['ldt']['3']);
		$document->setValue('ldt_4', $this->result['ldt']['4']);
		$document->setValue('ldt_94', $this->result['ldt']['94']);
		//длину основания датчика температуры
		$document->setValue('losn_all', $this->result['losn']['all']);
		$document->setValue('losn_3', $this->result['losn']['3']);
		$document->setValue('losn_4', $this->result['losn']['4']);
		//длина бобышки
		$document->setValue('lbob_all', $this->result['lbob']['all']);
		$document->setValue('lbob_3', $this->result['lbob']['3']);
		$document->setValue('lbob_4', $this->result['lbob']['4']);
		//минимальна разность температур 
		$document->setValue('dtmin', $this->project_quipment['preobTeplo']['dtmin']);
		//вес импульса
		$document->setValue('ves_imp_1', $this->resultOut(array('rashodomer','all','check_attribute', '228')));  
		$document->setValue('ves_imp_2', $this->resultOut(array('rashodomer','all','check_attribute', '228')));  
		$document->setValue('ves_imp_3', $this->resultOut(array('rashodomer','3','check_attribute', '228')));  
		$document->setValue('ves_imp_4', $this->resultOut(array('rashodomer','4','check_attribute', '228')));					
		$document->setValue('ves_imp_94', $this->resultOut(array('rashodomer','94','check_attribute', '228')));					
		$document->setValue('g_min_sys', $this->result['g_min_sys']);  
		//вставка таблицы				
		$document->cloneRowAndSetValues('n', $this->result['system_list']);
		//только для Питера
		if($this->tso == '2')
		{
			//вставка таблицы с системами и нагрузками
			$document->cloneRowAndSetValues('sys_title', $this->result['system_list_rashod']);
			$document->cloneRowAndSetValues('sys_title_q', $this->result['system_list_q']);
			$document->cloneRowAndSetValues('sys_title_g', $this->result['system_list_g']);
			$document->cloneRowAndSetValues('sys_title_gmax', $this->result['system_list_gmax']);
			$document->cloneRowAndSetValues('sys_potreb_day_title', $this->result['system_list_potreb_day']);
			$document->cloneRowAndSetValues('sys_potreb_month_title', $this->result['system_list_potreb_month']);
			$document->setValue('g_max_sys_piter', $this->result['g_max_sys_piter']);
			$document->setValue('g_min_sys_piter', $this->result['g_min_sys_piter']);
			
		}
		//вставка таблицы спецификации датчиков температуры
		$document->cloneRowAndSetValues('dt_all_n', $this->result['dt_spec']['all']);
		//вставка таблицы спецификации датчиков давления
		$document->cloneRowAndSetValues('dd_1_n', $this->result['dd_spec']['1']);
		$document->cloneRowAndSetValues('dd_2_n', $this->result['dd_spec']['2']);				
		//особые таблицы для 2 схемы
		if($this->scheme == '2')
		{
		}
		//особые таблицы для 3 схемы
		if($this->scheme == '3')
		{
			$document->cloneRowAndSetValues('dt_3_n', $this->result['dt_spec']['3']);
			$document->cloneRowAndSetValues('dd_3_n', $this->result['dd_spec']['3']);
		}
		//особые таблицы для 4 схемы
		if($this->scheme == '4')
		{
			$document->cloneRowAndSetValues('dt_3_n', $this->result['dt_spec']['3']);
			$document->cloneRowAndSetValues('dd_3_n', $this->result['dd_spec']['3']);
			$document->cloneRowAndSetValues('dd_4_n', $this->result['dd_spec']['4']);
		}			
		$document->cloneRowAndSetValues('tm_n', $this->getSpecificationTM());
		if(!empty($this->getSpecificationATM()))
		{
			$document->cloneRowAndSetValues('atm_n', $this->getSpecificationATM());
		}
		$document->saveAs($file_url);
		
		
		$zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
		
		for ($i = 1; $i <= 13; $i++) 
		{
			$zip->open($file_url);
			$fileToModify = "word/header".$i.".xml";
			$header = $zip->getFromName($fileToModify);
			$header = str_replace('${pr_numb}', $this->project_info['pr_numb'], $header);
			$header = str_replace('${raz}', $this->project_info['raz'], $header);
			$header = str_replace('${prov}', $this->project_info['prov'], $header);
			$header = str_replace('${gip}', $this->project_info['gip'], $header);
			$header = str_replace('${control}', $this->project_info['control'], $header);
			$header = str_replace('${date}', $this->project_info['date'], $header);		
			$zip->deleteName($fileToModify);
			$zip->addFromString($fileToModify, $header);
			$return =$zip->close();	
		}
		for ($i = 1; $i <= 11; $i++) 
		{
			$zip->open($file_url);
			$fileToModify = "word/footer".$i.".xml";
			$header = $zip->getFromName($fileToModify);
			$header = str_replace('${pr_numb}', $this->project_info['pr_numb'], $header);
			$header = str_replace('${raz}', $this->project_info['raz'], $header);
			$header = str_replace('${prov}', $this->project_info['prov'], $header);
			$header = str_replace('${gip}', $this->project_info['gip'], $header);
			$header = str_replace('${control}', $this->project_info['control'], $header);
			$header = str_replace('${date}', $this->project_info['date'], $header);		
			$zip->deleteName($fileToModify);
			$zip->addFromString($fileToModify, $header);
			$return =$zip->close();	
		}
		
		
		$type = '1';
		if($type == '1')
		{
			$converter_url = 'http://87.249.246.161:1967/converter.php?filename=print_pz.docx';
			
			$complete_file = file_get_contents($converter_url);
			
			$complete = file_get_contents('http://87.249.246.161:1967/complete/'.$complete_file);
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="the.pdf"');
			header('Content-Transfer-Encoding: binary');
			echo $complete ;
			exit;
		}
		elseif($type == '2')
		{	
			$query_scheme = $db->query("SELECT * FROM " . DB_PREFIX . "_metering WHERE d1 = '".$dn."' AND ddt = '".$ddt."' AND ldt = '".$d."'");
			if($db->numRows($query_scheme) > 0)
			{
				$r = $db->getRow($query_scheme);
				$scheme_url = config('app.url').'/download/'.$r['aid_scheme'];
				header( 'Location: '.$scheme_url); 
				exit;
			}
			else
			{
				$core->tpl->info('К сожалению файл схемы не найден, вскоре мы исправим это!', 'warning');
			}
			
		}
		elseif($type == '3')
		{
			exit;		
		}
	}
	
	public function getScheme($type = '1')		
	{
		global $core, $db;
		$query_scheme = $db->query("SELECT * FROM " . DB_PREFIX . "_metering WHERE scheme= '".$this->scheme."' AND type='".$type."' AND d1 = '".$this->project_dn['all']['dn']."' AND ddt = '".$this->project_dn['all']['ddt']."' AND ldt = '".$this->resultOut(array('rashodomer','all','check_attribute','4'))."'");
		//echo "SELECT * FROM " . DB_PREFIX . "_metering WHERE scheme= '".$this->scheme."' AND type='".$type."' AND d1 = '".$this->project_dn['all']['dn']."' AND ddt = '".$this->project_dn['all']['ddt']."' AND ldt = '".$this->resultOut(array('rashodomer','all','check_attribute','4'))."'";
		if($db->numRows($query_scheme) > 0)
		{
			$r = $db->getRow($query_scheme);
			$scheme_url = config('app.url').'/download/'.$r['aid_scheme'];
			header( 'Location: '.$scheme_url);  
			exit;
		}
		else
		{
			$core->tpl->info('К сожалению файл схемы не найден, вскоре мы исправим это!', 'warning');
		}
	}
	
	




}
