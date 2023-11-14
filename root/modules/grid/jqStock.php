<?php

class jqStock extends jqGrid
{
    protected function init()
    {
        global $attrQstr;
		$manufacture_types = $this->getManufactureTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'asc',
			'multiselect' => true,
        );
        $this->query = "SELECT {fields} FROM JMY_catalog as n LEFT JOIN JMY_catalog_manufacture as m on(n.man = m.id) WHERE n.qmin <> '' AND n.qnorm <> '' ".$attrQstr." AND {where} ";
        $this->cols = array(				
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'n.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 'n.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			'sku' => array(
				'label' => 'Артикул',
                'hidden' => false,
				'encode' => false,
				'db' => 'n.sku',
				'width' => 30,				
            ),	
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'n.title',
				'width' => 60,				
            ),	
			
			'manufacture' => array(
				'label' => 'Производитель',
				'width' => 60,
				'db' => 'n.manufacture',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $manufacture_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($manufacture_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'quantity' => array(
				'label' => 'На складе',
                'hidden' => false,
				'encode' => false,
				'db' => 'n.quantity',
				'width' => 40,				
            ),	
			
			'qmin' => array(
				'label' => 'Минимальное',
                'hidden' => false,
				'encode' => false,
				'db' => 'n.qmin',
				'width' => 40,				
            ),	
			
            'qnorm' => array(
				'label' => 'Оптимальное',
                'hidden' => false,
				'encode' => false,
				'db' => 'n.qnorm',
				'width' => 40,				
            ),	
			
			'qneed' => array(
				'label' => 'Необходимо закупить',
                'hidden' => false,
				'encode' => false,
				'manual' => true,
				'width' => 40,	
				'search_op' => 'like'
				
            ),	
			
			'bc' => array(
				'label' => 'Статус',
				'width' => 15,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
			'ab' => array(
				'label' => 'Действия',
				'width' => 50,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
        );

        $this->render_filter_toolbar = true;
    }
	
	protected function getManufactureTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_catalog_manufacture ORDER by name ASC");
        $rows = array();
		$rows[''] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {
            $rows[$r['id']] = htmlspecialchars($r['name']);
        }
        return $rows;
    }
	
	
    protected function parseRow($r)
    {    
		global $org_array, $lang, $db, $adminTpl, $core, $admin_conf, $lang, $attrQstr; 
		$r['numb'] = $r['id'];
		$query = $db->query("SELECT n.*,  c.id as cid, c.name, c.altname as alturl FROM ".DB_PREFIX."_catalog AS n LEFT JOIN ".DB_PREFIX."_categories AS c ON (n.cat=c.id) WHERE n.id = ".$r['id']." ".$attrQstr." LIMIT 1");
        $task = $db->getRow($query);        
		$r['ab'] = '<div class="btn-group">
						<a href = "https://atri-energo.ru/catalog/'.$task['altname'].'.html" class="btn btn-xs btn-primary" style="color: #fff;">Посмотреть на сайте</a>
					</div>';
        $status = '';
		$min = $task['qmin'];
		$opt = $task['qnorm'];
        if ($min >= $task['quantity']) 
		{
			$status = '<span class="fa fa-circle text-danger fa-md" data-toggle="tooltip" title="Необходимо провести закупку"></span>';						
		}
		elseif ($opt > $task['quantity']) 
		{
			$status = '<span class="fa fa-circle-o text-warning fa-md" data-toggle="tooltip" title="Скоро закончится"></span>';	
		}
		else
		{
			$status = '<span class="fa fa-check-circle text-success fa-md" data-toggle="tooltip" title="Все в порядке"></span>';
		}
        $r['bc'] = $status;
        $need = intval($r['qnorm'])-intval($r['quantity']);
		if ($need<=0)
		{
			$need = '0';
		}
		$r['qneed'] = $need;
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}