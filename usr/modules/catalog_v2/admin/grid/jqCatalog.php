<?php

class jqCatalog extends jqGrid
{
    protected function init()
    {
		global $url;
		 $this->table = 'JMY_catalog';
		//$this->nav = array('excel' => true);	
		$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
		$active_types = $this->getActiveTypes();
		//$specification_types = $this->getSpecification();
		$manufacture_types = $this->getManufactureTypes();
        $this->options = array(
            'sortname' => 'title',
            'sortorder' => 'asc',
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
		$hidden_catalog = isset($url[3]) ? "" : "o.hidden = '0' AND";
		
        $this->query = "SELECT {fields} 
		FROM `".DB_PREFIX."_catalog` as o 
		LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as m on(o.man = m.id) 
		LEFT JOIN (SELECT * FROM `".DB_PREFIX."_stock_detail` WHERE stock = 1) as sd ON(sd.cid = o.id)
		WHERE ".$hidden_catalog." {where} ";
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
				'label' => 'Активность',
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
						
			'specification' => array(
				'label' => 'Спецификация',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),	
			
			'quantity' => array(
				'label' => 'Остаток',
                'hidden' => false,
				'encode' => false,
				'db' => 'sd.sum',
				'null' => 0,
				'width' => 20,		
				'editable' => true,
				 'edittype' => "text",
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
		global $org_array;   
        $r['numb'] = $r['id'];
		
		$result = $this->DB->query("SELECT spec FROM JMY_catalog WHERE id = '".$r['id']."' LIMIT 1");
		$p = $this->DB->fetch($result);
        if(!empty($p)){
			$result = json_decode($p['spec'], true);
            if(!empty($result[1]) && !empty($result[2]))
			{
				$r['specification'] = "Заполнена";
			} else $r['specification'] = "Не заполнена";
		} else $r['specification'] = "Не заполнена";
        
		$r['sku'] = empty($r['sku']) ? 'Отсутствует' : $r['sku'];
		
		

		$r["ab"] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/catalog_v2/edit/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="copyCatModal(this);">Скопировать</a></li>  
						</ul>						
					</div>';	

		if((int)$r["hidden"] === 0){
			$r["ab"] .=	'<div class="btn-group">
			<button type="button" onclick="hiddenCat(1, '.$r['id'].');" class="btn btn-xs btn-danger ml5 mr5"><span class="fas fa-eye-slash" data-toggle="tooltip" title="Скрыть номенклатуру"></span></button>						
		</div>';
		} else {
			$r["ab"] .=	'<div class="btn-group">
			<button type="button" onclick="hiddenCat(0, '.$r['id'].');" class="btn btn-xs btn-success ml5 mr5"><span class="fas fa-eye" data-toggle="tooltip" title="Показать номенклатуру"></span></button>						
		</div>';
		}

		$r["ab"] .= '<div class="btn-group">
			<button type="button" style="display:none;" onclick="copyCatModal(this);" class="copy btn btn-xs btn-system mr5" data-id="'.$r['id'].'"><span class="far fa-clone" data-toggle="tooltip" title="Скопировать"></span></button>		
		</div>
		<div class="btn-group">
			<button type="button" onclick="location.href = \''.ADMIN.'/realization/order/' . $r['id'] . '\'" class="btn btn-xs btn-success mr5"><span class="fal fa-globe " data-toggle="tooltip" title="Открыть на сайте"></span></button>						
		</div>';

		

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}