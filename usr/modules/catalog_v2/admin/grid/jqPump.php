<?php

class jqPump extends jqGrid
{
    protected function init()
    {
        global $core, $url;

        $this->table = 'JMY_pump';
        $uid = $core->auth->user_info['id'];
        //$this->nav = array('excel' => true);
        $cat_types = $this->getCatTypes();
        $category_types = $this->getCategoryTypes();
        $active_types = $this->getActiveTypes();
        //$specification_types = $this->getSpecification();
        $manufacture_types = $this->getManufactureTypes();
        $type_of_instalation = $this->getTypeOfInstalation();
        $work_line = $this->getWorkLine();
        $uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'DESC',
            'multiselect' => true,
        );

        $this->nav = array(
            'excel' => true,
            'exceltext' => 'Сохранить в Excel',
            'fastedit' => true,
        );

        $this->query = "SELECT {fields} FROM JMY_pump as p LEFT JOIN JMY_catalog as c ON(c.id = p.cid) LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id = c.man) WHERE `deleted_at` IS NULL AND {where} ";
        $this->cols = array(


            #Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'p.id',
            ),

            'numb' => array(
                'label' => '№',
                'width' => 15,
                'db' => 'p.id',
                'hidden' => false,
                'search_op' => 'like'
            ),

            'cid' => array(
                'label' => 'ID каталога',
                'hidden' => false,
                'encode' => false,
                'db' => 'c.id',
                'width' => 30,
                'search_op' => 'like',
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
            'type_of_instalation' => array(
                'label' => 'Тип установки',
                'width' => 30,
                'db' => 'p.type_of_instalation',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $type_of_instalation,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($type_of_instalation, 'Все'),
                ),
                'search_op' => 'equal',
            ),
            'work_line' => array(
                'label' => 'Рабочая линия',
                'width' => 30,
                'db' => 'p.work_line',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $work_line,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($work_line, 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'type' => array(
                'label' => 'Тип графика',
                'hidden' => false,
                'encode' => false,
                'db' => 'p.type',
                'width' => 30,
                'editable' => false,
                'search_op' => 'like',
            ),

            'priority' => array(
                'label' => 'Приоритетный график',
                'width' => 30,
                'db' => 'p.priority',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => [0 => 'Нет', 1 => 'Да'],
                'editable' => true,
                'edittype' => "select",
                'editoptions' => array(
                    'value' => new jqGrid_Data_Value([0 => 'Нет', 1 => 'Да'])
                ),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value([0 => 'Нет', 1 => 'Да'], 'Все')
                ),
                'search_op' => 'equal',
            ),

            'uid' => array(
                'label' => 'Создатель',
                'db' => 'p.uid',
                'width' => 30,
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

            'data' => array(
                'label' => 'Точки',
                'hidden' => false,
                'encode' => false,
                'db' => 'p.data',
                'width' => 40,
                'search_op' => 'like',
            ),

            'ab' => array(
                'label' => 'Выберите действие',
                'width' => 40,
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
        while ($r = $this->DB->fetch($result)) {
            $rows[',' . $r['id'] . ','] = $r['name'];
        }
        return $rows;
    }

    protected function getCategoryTypes()
    {
        global $core;
        $result = $this->DB->query("SELECT * FROM JMY_categories WHERE module = 'catalog_v2' ORDER by name ASC");
        $rows = array();
        $rows[''] = 'Без категории';

        $cats_arr = $core->aCatList('catalog_v2');
        foreach ($cats_arr as $cid => $name) {
            //$selected = ($pid == $cid) ? "selected" : "";
            $rows[',' . $cid . ','] = $name;
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
        while ($r = $this->DB->fetch($result)) {
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }

    protected function getTypeOfInstalation()
    {
        return \Catalog_v2\Model\Pump::getTypeOfInstalation();
    }

    protected function getWorkLine()
    {
        return \Catalog_v2\Model\Pump::getWorkLine();
    }


    protected function parseRow($r)
    {
        global $org_array;
        $r['numb'] = $r['id'];

        $r['sku'] = empty($r['sku']) ? 'Отсутствует' : $r['sku'];


        $r["ab"] = '<div class="btn-group">
						<button type="button" onclick="location.href = \'' . ADMIN . '/module/catalog_v2/pump/edit/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Изменить</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="del_modal(' . $r["id"] . ');">Удалить</a></li>  
						</ul>						
					</div>';

        if (!empty($r["data"])) {
            $data = json_decode($r["data"], true);
            if (is_array($data)) {
                $end = array_pop($data);
                // $r["data"] = json_encode($end);
                $r["data"] = 'Q: ' . $end['Q'] . '<br>' . 'H: ' . $end['H'];
            }
        }

        return $r;
    }

    protected function getUidTypes()
    {

        $result = $this->DB->query("SELECT p.* FROM JMY_pump as p GROUP BY uid");
        $rows = array();
        $rows[0] = 'Не указан';
        while ($r = $this->DB->fetch($result)) {
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