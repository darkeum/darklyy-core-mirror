<?php

class jqCertView extends jqGrid
{
    protected function init()
    {
		global $url;
		//$this->nav = array('excel' => true);	
		$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
		$active_types = $this->getActiveTypes();
		$manufacture_types = $this->getManufactureTypes();
        $this->options = array(
            'sortname' => 'title',
            'sortorder' => 'asc',
			'multiselect' => true,
        );
		$cert_id = intval($url[5]);
        $this->query = "SELECT {fields} 
		FROM JMY_catalog as o LEFT JOIN 
		JMY_catalog_manufacture as m on(o.man = m.id) 
		WHERE o.id IN(SELECT cid FROM JMY_cert_relation WHERE cert_id = '".$cert_id."') AND {where} ";
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
					
			'sku' => array(
				'label' => 'Артикул',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.sku',
				'width' => 30,				
            ),	
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.title',
				'width' => 60,				
            ),	
			
			'manufacture' => array(
				'label' => 'Производитель',
				'width' => 30,
				'db' => 'o.man',
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
			
			'category' => array(
				'label' => 'Категории',
				'width' => 30,
				'db' => 'o.category',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $category_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($category_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),	

			'active' => array(
				'label' => 'Статус',
				'width' => 30,
				'db' => 'o.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $active_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($active_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),					
						
			'ab' => array(
				'label' => 'Действия',
				'width' => 30,
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
   
	
	protected function getCatTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_categories WHERE module = 'catalog' ORDER by name ASC");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {
            $rows[','.$r['id'].','] = $r['name'];
        }
        return $rows;
    }
	
	protected function getCategoryTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_categories WHERE module = 'catalog_v2' ORDER by name ASC");
        $rows = array();
		$rows[''] = 'Без категории';
        while($r = $this->DB->fetch($result))
        {
            $rows[','.$r['id'].','] = $r['name'];
        }
        return $rows;
    }
	
	protected function getActiveTypes()
    {
		$rows['1'] = 'Включен';
		$rows['0'] = 'Отключен';
        return $rows;
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
		global $org_array, $url;   
        $r['numb'] = 'TN-'.$r['id'];	 
		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/catalog_v2/cert/delete/' . $r['id'] . '/'.$url[5].'\'" class="btn btn-xs btn-primary">Удалить</button>
					</div>';
		
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}