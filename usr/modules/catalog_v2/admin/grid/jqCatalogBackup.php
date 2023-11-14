<?php

class jqCatalogBackup extends jqGrid
{
    protected function init()
    {
		global $url;
		//$this->nav = array('excel' => true);	
		$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
		$active_types = $this->getActiveTypes();
		//$specification_types = $this->getSpecification();
		$manufacture_types = $this->getManufactureTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
			'multiselect' => true,
			 'onSelectRow' => new jqGrid_Data_Raw("function(id){ 
			  if (id && id !== lastSelection) {
                    var grid = $(\"#jqCatalog\");
                    grid.jqGrid('restoreRow',lastSelection);
                    grid.jqGrid('editRow',id, {keys: true} );
                    lastSelection = id;
                }
}"),
		);
		
        $this->query = "SELECT {fields} 
		FROM `".DB_PREFIX."_catalog` as o 
		LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as m on(o.man = m.id) 
		WHERE {where} ";
        $this->cols = array(		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'o.id',
            ),
            'hidden' => array('hidden' => true,
                'db' => 'o.hidden',
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
				'search_op' => 'like',
            ),	
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.title',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
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
	
			// 'ab' => array(
			// 	'label' => 'Выберите действие',
			// 	'width' => 60,
			// 	'hidden' => false,
			// 	'manual' => true,
			// 	'encode' => false,
			// 	'sortable' => false,			
            //     'search_op' => false,
			// 	'search' => false,
			// 	'title' => false,
            // ),
            	
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
		global $core;
        $rows = array();
		$rows[''] = 'Без категории';
    
		$cats_arr = $core->aCatList('catalog_v2');
		foreach ($cats_arr as $cid => $name) {
			//$selected = ($pid == $cid) ? "selected" : "";
			   $rows[','.$cid.','] = $name;
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
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }
	


    protected function parseRow($r)
    {
        $r['numb'] = $r['id'];
		$pArray = array();
		$pArray['cid'] = $r['id'];
		$r['title'] = html_entity_decode($r["title"], ENT_QUOTES);		
        
		$r['sku'] = empty($r['sku']) ? 'Отсутствует' : $r['sku'];

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}