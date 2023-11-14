<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2020 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */


namespace Api\App\Method;

use \Scan\Scan as BaseScan;

class Scan extends \Api\App\AppMethodLoader
{
	protected $model = \Sales\Model\Sales::class;
	protected $table = 'sales';

	public function file()
	{
		$scan = new BaseScan;
		return $scan->upload();
	}

	public function do()
	{
		$scan = new BaseScan;
		return $scan->do();
	}

	
	public function pdf()
	{
		$scan = new BaseScan;
		$scan->getPdf();
	}

	public function catalog()
	{
		if(!empty($_POST['cid'])){
			$catalog = \Catalog_v2\Model\Catalog::find($_POST['cid']);
			if(!empty($catalog)) {
				$result = [];
				$result['type'] ='success';
				$result['catalog'] = $catalog;
			} else {
				$result['type'] ='error';
				$result['error'] = 'Item not found';
			}			
		} else {
			$result['type'] ='error';
			$result['error'] = 'Invalid catalog id';
		}		
		return $result;
	}

	public function save()
	{
		$scan = new BaseScan;
		$scan->save($this->uid);
		return ['type' => 'success'];
	}
}
