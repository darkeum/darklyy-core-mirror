<?php

class jqManufacture extends jqGrid
{
    protected function init()
    {
		//$this->nav = array('excel' => true);	
		$cat_types = $this->getCatTypes();
		$category_types = $this->getCategoryTypes();
        $this->options = array(
            'sortname' => 'name',
            'sortorder' => 'asc',
        );
        $this->query = "SELECT {fields} FROM JMY_catalog_manufacture as o WHERE {where} ";
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
					
			'name' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'o.name',
				'width' => 60,				
            ),	
						
			'ab' => array(
				'label' => 'Выберите действие',
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
   
	
	protected function getCatTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_categories WHERE module = 'catalog'");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {
            $rows[','.$r['id'].','] = $r['name'];
        }
        return $rows;
    }
	
	protected function getCategoryTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_categories WHERE module = 'catalog_v2'");
        $rows = array();
		$rows[''] = 'Без категории';
        while($r = $this->DB->fetch($result))
        {
            $rows[','.$r['id'].','] = $r['name'];
        }
        return $rows;
    }
	


    protected function parseRow($r)
    {
		global $org_array;   
        $r['numb'] = 'CM-'.$r['id'];	
        
        $r["ab"] = '<a href="javascript:void(0);" onclick="addManufacture(this);" class="btn btn-primary btn-sm" data-edit="'.$r['id'].'" data-name="'.$r["name"].'">Редактировать</a>';

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}