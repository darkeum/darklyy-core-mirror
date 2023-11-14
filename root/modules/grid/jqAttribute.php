<?php

class jqAttribute extends jqGrid
{
    protected function init()
    {	
		$types = $this->getTypes();
		$status = $this->getStatus();
        $this->options = array(
            'sortname' => 'title',
            'sortorder' => 'asc',
        );
        $this->query = "SELECT {fields} FROM JMY_catalog_atribute as o WHERE {where} ";
        $this->cols = array(		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'o.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 'o.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.title',
				'width' => 60,				
            ),	
			
			
			
			'type' => array(
				'label' => 'Тип атрибута',
				'width' => 30,
				'db' => 'o.type',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'value' => array(
				'label' => 'Значения',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.value',
				'width' => 60,				
            ),	
			
			'status' => array(
				'label' => 'Статус',
				'width' => 30,
				'db' => 'o.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status, 'Все'),
                ),
                'search_op' => 'equal',
            ),
		);

        $this->render_filter_toolbar = true;
    }
   
	
	protected function getTypes()
    {
      
        $rows = array();
        $rows['1'] = 'Список';
		$rows['2'] = 'Строка';
        return $rows;
    }
	
	protected function getStatus()
    {
        $rows = array();
        $rows['1'] = 'Активен';
		$rows['2'] = 'Отключен';
        return $rows;
    }
	


    protected function parseRow($r)
    {
		global $org_array;   
        $r['numb'] = 'AT-'.$r['id'];
		//$price = get_price($r['price']);
		//$r['price'] = $price['is'];	     
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}