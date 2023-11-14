<?php

class jqChangeLog extends jqGrid
{
    protected function init()
    {
		global $url;
		//$this->table = 'JMY_catalog';
		//$this->nav = array('excel' => true);	
		//$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
		$active_types = $this->getActiveTypes();
		$types = $this->getTypes();
		//$specification_types = $this->getSpecification();
		$uid_types = $this->getUidTypes();
		$manufacture_types = $this->getManufactureTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
		);

        $this->query = "SELECT {fields} FROM JMY_catalog_change_log as ccl LEFT JOIN JMY_catalog as c ON(c.id = ccl.cid) LEFT JOIN JMY_catalog_manufacture as m on(c.man = m.id) WHERE {where} ";
        $this->cols = array(		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'ccl.id',
            ),
            'cid' => array('hidden' => true,
                'db' => 'ccl.cid',
            ),
            'status' => array('hidden' => true,
                'db' => 'ccl.status',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 'ccl.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			'sku' => array(
				'label' => 'Артикул',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.sku',
				'width' => 30,		
				'search_op' => 'like',
            ),	
					
			'title' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.title',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
            ),	
			
			'manufacture' => array(
				'label' => 'Производитель',
				'width' => 30,
				'db' => 'c.man',
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
				'db' => 'c.category',
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

			'status_num' => array(
				'label' => 'Статус',
				'width' => 30,
				'db' => 'ccl.status',
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
			'type' => array(
				'label' => 'Тип',
				'width' => 30,
				'db' => 'ccl.type',
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
			
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'ccl.uid',
                'width' => 25,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			
			'ab' => array(
				'label' => 'Выберите действие',
				'width' => 60,
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
		global $core;
		$result = $this->DB->query("SELECT cat.* FROM JMY_catalog_change_log as ccl LEFT JOIN JMY_catalog as c ON(c.id = ccl.cid) LEFT JOIN JMY_categories as cat ON(c.category = CONCAT(',', cat.id, ','))");
		$test_array = [];
		while($r = $this->DB->fetch($result)){
			$test_array[] = $r["id"];
		}
		//var_dump($test_array);
        $rows = array();
		$rows[''] = 'Без категории';
    
		$cats_arr = $core->aCatList('catalog_v2');
		foreach ($cats_arr as $cid => $name) {
			if(in_array($cid, $test_array)){
				$rows[','.$cid.','] = $name;
			}
		}
        return $rows;
    }
	
	protected function getActiveTypes()
    {
		$rows['1'] = 'Ожидает';
		$rows['2'] = 'Просмотрено';
        return $rows;
    }
	protected function getTypes()
    {
		$rows['1'] = 'Создана';
		$rows['2'] = 'Изменена';
        return $rows;
    }
        
	
	protected function getManufactureTypes()
    {
        $result = $this->DB->query("SELECT cm.* FROM JMY_catalog_change_log as ccl LEFT JOIN JMY_catalog as c ON(c.id = ccl.cid) LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id = c.man) ORDER by name ASC");
        $rows = array();
		$rows[''] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {
            $rows[$r['id']] = htmlspecialchars($r['name']);
        }
        return $rows;
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT ccl.* FROM JMY_catalog_change_log as ccl ");
        $rows = array();
		$rows[0] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {	
			$result_row = shortUserName($r["uid"]);
            $rows[$r['uid']] = $result_row;
        }
        return $rows;
    }

    protected function parseRow($r)
    {
		global $org_array;   
        $r['numb'] = 'JN-'.$r['id'];
        
		$r['sku'] = empty($r['sku']) ? 'Отсутствует' : $r['sku'];
		
		

		$r["ab"] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/catalog_v2/edit/' . $r['cid'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>					
					</div>
					<div class="btn-group" style="'.($r["status"] == 1 ? '' : 'display: none;').'">
					<button type="button" onclick="confirm_view('.$r['id'].');" class="btn btn-xs btn-success mr5" data-id="'.$r['id'].'"><span class="fas fa-check" data-toggle="tooltip" title="Просмотрено"></span></button>		
				</div>';	

		

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}