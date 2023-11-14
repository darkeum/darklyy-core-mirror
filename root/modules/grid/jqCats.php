<?php

class jqCats extends jqGrid
{
    protected function init()
    {
		global $lang;
		$this->table = 'JMY_categories';
		$module_types = $this->getModuleTypes();
		$hidden_types = $this->getHiddenTypes();
		$hidden_types_select = $this->getHiddenTypes(true);
        $this->options = array(
            'sortname' => 'name',
            'sortorder' => 'asc',
        );
        $this->query = "SELECT {fields} FROM JMY_categories as c LEFT JOIN JMY_plugins as p ON(p.title = c.module) WHERE p.categories = 1 AND {where} ";
        $this->cols = array(				
		
            'id' => array('hidden' => true,
                'db' => 'c.id',
            ),
			
			'altname' => array('hidden' => true,
                'db' => 'c.altname',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 10,
				'db' => 'c.id',
				'hidden' => false,
				'search_op' => 'like'
            ),				
							
			'name' => array(
				'label' => 'Наименование',
                'hidden' => false,
				'encode' => false,
				'db' => 'c.name',
				'width' => 30,				
            ),	
			
			'cd' => array(
				'label' => 'Путь',
                'hidden' => false,
				'encode' => false,
				'manual' => true,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
				'width' => 60,				
            ),				
			
			'module' => array(
				'label' => 'Модуль',
				'width' => 20,
				'db' => 'c.module',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $module_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($module_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'position' => array(
				'label' => 'Позиция',
                'hidden' => false,
				'align' => 'center',     
				'encode' => false,
				'db' => 'c.position',
				'width' => 10,	
				'editable' => true,
            ),	
			
			'hidden' => array(			
				'label' => 'Видимость',
                'db' => 'c.hidden',
                'width' => 10,              
                'align' => 'center',              
				'hidden' => false,
				'encode' => false,
				'replace' => $hidden_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($hidden_types_select, $lang['all']),				
                ),
                'search_op' => 'equal',
            ), 	
			
			'ab' => array(
				'label' => $lang['action'],
				'width' => 40,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            )
        );

        $this->render_filter_toolbar = true;
    }
	
	protected function getModuleTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_plugins WHERE categories = 1");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {
            $rows[$r['title']] = $r['content'];
        }
        return $rows;
    }
	
	protected function getHiddenTypes($select = false)
    { 
		global $lang;
        $rows_select = array();	
		$rows_select[0] = 'Активны';
		$rows_select[1] = 'Скрыты';		
		
        $rows = array();
		$rows[0] = "<span class='far fa-eye text-success fa-md' data-toggle='tooltip' title='Активны'></span>";
		$rows[1] = "<span class='far fa-eye-slash text-danger fa-md' data-toggle='tooltip' title='Скрыты'></span>";      
        return ($select ?  $rows_select :  $rows);
    }

    protected function parseRow($r)
    {
		global $core, $lang;  		
		$cats_arr = $core->aCatList();
        $r['numb'] = module_perfix('categories', $r['id']);        
		$r['cd'] = (isset($cats_arr[$r['id']]) ? $cats_arr[$r['id']] : '');
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/cats/edit/' . $r['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="/'.$r['module'].'/' . $r['altname'] . '">Открыть категорию</a></li> 
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-del-'. $r['id'].'\')">'.$lang['delete'].'</a></li> 
						</ul>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-active-'. $r['id'].'\');" class="btn btn-xs btn-'.($r['hidden'] == 0 ? 'success' : 'warning').'"><span style="    font-size: 14px;" class="far fa-eye" data-toggle="tooltip" title="'.($r['hidden'] == 0 ? '' : '').'"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="window.open(\'/'.$r['module'].'/' . $r['altname'] . '\')" class="btn btn-xs btn-system"><span class="far fa-link
" data-toggle="tooltip" title=""></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-del-'. $r['id'].'\')" class="btn btn-xs btn-danger"><span class="far fa-trash-alt" data-toggle="tooltip" title="'.$lang['delete'].'"></span></button>						
					</div>
					';									
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}