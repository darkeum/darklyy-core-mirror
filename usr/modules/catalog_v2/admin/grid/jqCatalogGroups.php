<?php

class jqCatalogGroups extends jqGrid
{
    protected function init()
    {
		global $url;
		$this->table = 'JMY_catalog_groups';
		//$this->nav = array('excel' => true);	
		$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
		$active_types = $this->getActiveTypes();
		//$specification_types = $this->getSpecification();
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'DESC',
			'multiselect' => true,
		);
		
        $this->query = "SELECT {fields} FROM JMY_catalog_groups as cg 
		LEFT JOIN (
			SELECT COUNT(id) as count, catalog_groups_id FROM JMY_catalog GROUP BY catalog_groups_id
		) as c ON(c.catalog_groups_id = cg.id)
		WHERE {where} ";
        $this->cols = array(		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'cg.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 'cg.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			// 'sku' => array(
			// 	'label' => 'Артикул',
            //     'hidden' => false,
			// 	'encode' => false,
			// 	'db' => 'c.sku',
			// 	'width' => 30,		
			// 	'search_op' => 'like',
            // ),	
					
			'name' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'cg.name',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
			),	

			'category' => array(
				'label' => 'Категории',
				'width' => 30,
				'db' => 'cg.category',
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
			
			// 'manufacture' => array(
			// 	'label' => 'Производитель',
			// 	'width' => 30,
			// 	'db' => 'c.man',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $manufacture_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($manufacture_types, 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),			
			// 'type_of_instalation' => array(
			// 	'label' => 'Тип установки',
			// 	'width' => 30,
			// 	'db' => 'p.type_of_instalation',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $type_of_instalation,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($type_of_instalation, 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),			
			// 'work_line' => array(
			// 	'label' => 'Рабочая линия',
			// 	'width' => 30,
			// 	'db' => 'p.work_line',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $work_line,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($work_line, 'Все'),
            //     ),
            //     'search_op' => 'equal',
			// ),			

			'comment' => array(
				'label' => 'Комментарий',
                'hidden' => false,
				'encode' => false,
				'db' => 'cg.comment',
				'null' => 'Не указано',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
            ),	

			'count' => array(
				'label' => 'Количество',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.count',
				'null' => '0',
				'width' => 60,		
				'editable' => false,
				'search_op' => 'like',
            ),	
			
			'uid' => array(			
				'label' => 'Создатель',
                'db' => 'cg.uid',
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
        $rows = array();
		$rows[''] = 'Без категории';
    
		$cats_arr = $core->aCatList('catalog_v2');
		foreach ($cats_arr as $cid => $name) {
			//$selected = ($pid == $cid) ? "selected" : "";
			   $rows[$cid] = $name;
		}
        return $rows;
    }
	
	protected function getActiveTypes()
    {
		$rows['1'] = 'Включен';
		$rows['0'] = 'Отключен';
        return $rows;
    }


    protected function parseRow($r)
    {
		global $org_array;   
        $r['numb'] = $r['id'];
		
		$r['sku'] = empty($r['sku']) ? 'Отсутствует' : $r['sku'];
		
		$r["name"] = ee($r["name"]);

		$r["ab"] = '<div class="btn-group">
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="del_m('.$r["id"].');">Удалить</a></li>  
						</ul>						
					</div>';

		

        return $r;
	}
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT cg.uid FROM JMY_catalog_groups as cg");
        $rows = array();
		$rows[0] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {	
			$result_row = shortUserName($r["uid"]);
            $rows[$r['uid']] = $result_row;
        }
        return $rows;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}