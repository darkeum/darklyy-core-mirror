<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Api\App\Method\Adapter\Request;

use \Project\Calculation\MeteringCalculation;

class MeteringStation {		
	protected $requestData = array();

	public function __construct($requestData){
		$this->requestData = $requestData; 
	}

	public function getAdapterData(){		
		
		$result = array();
		$result['id'] = $this->requestData['id'];
		$result['pid'] = $this->requestData['pid'];
		$result['type'] = $this->requestData['type'];	
		$result['object_name'] = $this->requestData['object_name'];	
		$result['fullTitle'] = 'Расчет узла учета №'.$this->requestData['id'];
		$result['description'] = 'Узел учета тепловой энергии - комплекс приборов и устройств, обеспечивающих учет тепловой энергии, массы (объема) теплоносителя, а также контроль и регистрацию его параметров. Конструктивно узел учета представляет собой набор "модулей", которые врезаются в трубопроводы.';
		$result['preview'] = config('app.url').'/media/project/meteringStation.png';
		$result['scheme'] = '/selection/scheme/'.$this->requestData['id'];
	
		

		$dir    = ROOT.'/files/request/'.$this->requestData['id'].'/';
		$files1 = scandir($dir);


		$result['project'] = '/files/request/'.$this->requestData['id'].'/'.$files1[2];
		$result['print'] = '/files/request/'.$this->requestData['id'].'/'.$files1[2];

		$mc = new MeteringCalculation;
		$mc->setRequestId($this->requestData['id']);
		$data = $mc->getResultFromSQL();
		$nSys = [];
		foreach ($data['project_system']['system'] as $key => $value) {
			$nSys[] = $value;
		}
		$data['project_system']['system'] = $nSys;
		$result['data'] = $data;
		$result['date'] = $this->requestData['date'];
		$result['dateView'] = date('d.m.Y H:i:s', strtotime($this->requestData['date']));
		$result['dateOnly'] = date('d.m.Y', strtotime($this->requestData['date']));
		return $result;
	}
}	
	
?>