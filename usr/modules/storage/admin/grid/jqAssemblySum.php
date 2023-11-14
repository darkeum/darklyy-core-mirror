<?php

use Boot\Support\Numb;
use Sales\Model\SalesDetail;
use Storage\Models\StorageAssemblyDetail;

class jqAssemblySum extends jqGrid
{
    protected function init()
    {
        global $lang;
        $uid_types = $this->getUidTypes();
        $status_types = $this->getStatusTypes();
        $types = $this->getTypes();
        $status_types_select = $this->getStatusTypes(true);
        $types_select = $this->getTypes(true);
        $del = $this->getDel();
        $del_select = $this->getDel(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->nav = array(
            #Set common excel export	
            'trash' => true,
            'trashtext' => 'Показать удаленные',
            'trashfield' => 'sa.del',
            'excel' => true,
			'exceltext' => 'Excel',

        );
        $this->do_search = true;
        $this->multipleSearch = true;
        $this->query = "SELECT {fields} 
        FROM JMY_storage_assembly as sa 
        LEFT JOIN JMY_sales as s ON(sa.sales_id = s.id) 
        LEFT JOIN JMY_organizations as o ON(s.oid = o.id) 
        LEFT JOIN JMY_object as obj ON(obj.id = s.object) 
        WHERE {where} ";
        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'sa.id',
            ),
            'sales_real' => array(
                'hidden' => true,
                'db' => 's.id',
            ),
            'oid_real' => array(
                'hidden' => true,
                'db' => 's.oid',
            ),
            'object_real' => array(
                'hidden' => true,
                'db' => 'obj.id',
            ),
            'sales_real' => array(
                'hidden' => true,
                'db' => 'sa.sales_id',
            ),

            'numb' => array(
                'label' => '№',
                'width' => 15,
                'db' => 'sa.id',
                'hidden' => false,
                'search_op' => 'like'
            ),

            'sales' => array(
                'label' => $lang['sales'],
                'width' => 20,
                'db' => 's.numb',
                'hidden' => false,
                'encode' => false,
                'search_op' => 'like',
                'null' => $lang['not_specified'],
            ),

            'oid' => array(
                'label' => $lang['oid'],
                'hidden' => false,
                'db' => 'o.name',
                'encode' => false,
                'width' => 40,
                'null' => $lang['not_specified'],
            ),

            'object' => array(
                'label' => 'Объект',
                'hidden' => false,
                'db' => 'obj.name',
                'encode' => false,
                'width' => 40,
                'null' => $lang['not_specified'],
            ),

            'uid' => array(
                'label' => $lang['uid'],
                'db' => 'sa.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, $lang['all']),
                ),
                'search_op' => 'equal',
            ),

            'date' => array(
                'label' => $lang['date'],
                'db' => 'sa.date',
                'width' => 35,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => presetRanges(),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',
                    ),
                ))),
                'search_op' => 'date_range',
            ),

            'type' => array(
                'label' => $lang['type'],
                'db' => 'sa.type',
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'replace' => $types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($types_select, $lang['all']),
                ),
                'search_op' => 'equal',
            ),

            'sum_price' => array(
                'label' => 'Сумма',
                'width' => 20,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            'sum_count' => array(
                'label' => 'Кол-во',
                'width' => 20,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            'sum_count_assembly' => array(
                'label' => 'Кол-во собранного',
                'width' => 20,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            'sum_weight' => array(
                'label' => 'Вес',
                'width' => 20,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            'status' => array(
                'label' => $lang['status'],
                'db' => 'sa.status',
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'replace' => $status_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'defaultValue' => '1',
                    'value' => new jqGrid_Data_Value($status_types_select, $lang['all']),
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
            ),

        );

        $this->render_filter_toolbar = true;
    }


    protected function searchOpDateRange($c, $val)
    {
        if (strpos($val, ' - ') !== false) {
            list($start, $end) = explode(' - ', $val, 2);
            $start = strtotime(trim($start));
            $end = strtotime(trim($end));
            if (!$start or !$end) {
                throw new jqGrid_Exception('Invalid date format');
            }
            #Stap dates if start is bigger than end
            if ($start > $end) {
                list($start, $end) = array($end, $start);
            }
            $start = date('Y-m-d', $start);
            $end = date('Y-m-d', $end);
            return $c['db'] . " BETWEEN '$start' AND '$end'";
        }
        $val = strtotime(trim($val));
        if (!$val) {
            throw new jqGrid_Exception('Invalid date format');
        }
        $val = date('Y-m-d', $val);
        return "DATE({$c['db']}) = '$val'";
    }

    protected function initDatepicker($options = null)
    {
        $options = is_array($options) ? $options : array();
        return new jqGrid_Data_Raw('function(el){$(el).datepicker(' . jqGrid_Utils::jsonEncode($options) . ');}');
    }

    protected function initDateRangePicker($options = null)
    {
        $options = is_array($options) ? $options : array();
        return new jqGrid_Data_Raw('function(el){$(el).daterangepicker(' . jqGrid_Utils::jsonEncode($options) . ');}');
    }

    protected function getUidTypes()
    {
        global $lang;
        $result = $this->DB->query("SELECT u.surname, u.name, u.ochestvo, u.id FROM JMY_storage_assembly AS sa LEFT JOIN JMY_users AS u ON (sa.uid=u.id) ORDER BY u.surname");
        $rows = array();
        $rows[0] = $lang['not_specified'];
        while ($r = $this->DB->fetch($result)) {
            $result_row = $r['surname'] . ' ';
            $result_row .= mb_substr($r['name'], 0, 1) . '. ';
            $result_row .= mb_substr($r['ochestvo'], 0, 1) . '.';
            $rows[$r['id']] = $result_row;
        }
        return $rows;
    }

    protected function getStatusTypes($select = false)
    {
        global $lang;
        $rows_select = array();
        $rows_select[1] = "Ожидает";
        $rows_select[2] = "Выполнено";

        $rows = array();
        $rows[1] = "<span class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='" . $rows_select[1] . "'></span>";
        $rows[2] = "<span class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='" . $rows_select[2] . "'></span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function getTypes($select = false)
    {
        global $lang;
        $rows_select = array();
        $rows = array();
        $rows_select[1] = 'Сборка товаров';
        $rows_select[2] = 'Отмена сборки';

        $rows[1] = "<span class='fas fa-people-carry text-success fa-md' data-toggle='tooltip' title='" . $rows_select[1] . "'></span>";
        $rows[2] = "<span class='fas fa-people-carry text-danger fa-md' data-toggle='tooltip' title='" . $rows_select[2] . "'></span>";

        return ($select ?  $rows_select :  $rows);
    }

    protected function getDel($select = false)
    {
        global $lang;
        $rows_select = array();
        $rows = array();
        $rows_select[0] = 'В работе';
        $rows_select[1] = 'В архиве';

        $rows[0] = "<span class='fas fas fa-archive text-danger fa-md' data-toggle='tooltip' title='" . $rows_select[0] . "'></span>";
        $rows[1] = "<span class='fas fas fa-archive text-success fa-md' data-toggle='tooltip' title='" . $rows_select[1] . "'></span>";

        return ($select ?  $rows_select :  $rows);
    }


    protected function parseRow($r)
    {
        global $lang;
        $sum_count = 0;
        $sum_count_assembly = 0;
        $sum_weight = 0;
        $sum_price = 0;
        foreach(StorageAssemblyDetail::where('said', $r["id"])->get() as $value){
            $sum_count = $sum_count + $value->count_save;
            $sum_count_assembly = $sum_count_assembly + $value->count;           
            if(!empty($value->sdid)){
                $sales_detail = SalesDetail::getPurchase($value->sdid);
                $sum_price = $sum_price + (($sales_detail->price ?? 0)*$value->count);  
                if(!empty($sales_detail->sales_detail)) {
                    $weight = Numb::prepareNumeric( $sales_detail->sales_detail->catalog->weight['1'] ?? 0);      
                } else {
                    $weight = 0;
                }                  
                $sum_weight = $sum_weight + $weight * $value->count;
            } else {
                $sum_weight = $sum_weight + 0;
                $sum_price = $sum_price + 0;
            }
           
        }
        $r['sum_count'] = $sum_count;
        $r['sum_count_assembly'] = $sum_count_assembly;
        $r['sum_weight'] = $sum_weight;
        $r['sum_price'] = $sum_price;

        $r['oid'] = empty($r['oid_real']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="' . ADMIN . '/organizations/edit/' . $r['oid_real'] . '" target="_blank">' . $r['oid'] . '</a>';
        $r['object'] = empty($r['object_real']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="/administration//object/view/' . $r['object_real'] . '" target="_blank">' . $r['object'] . '</a>';
        $r['sales'] = empty($r['sales_real']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="' . ADMIN . '/module/sales/order/' . $r['sales_real'] . '" target="_blank">' . $r['sales'] . '</a>';
        $r['numb'] = $r['id'];
        $r['ab'] = '<div class="btn-group">
            <button type="button" onclick="location.href = \'' . ADMIN . '/module/storage/assembly/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>	
            <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
				<ul role="menu" class="dropdown-menu">
					<li><a href="javascript:void(0)" onclick="delModal(' . $r["id"] . ');">' . $lang['delete'] . '</a></li> 
				</ul>				
		</div>';

        if (is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));

        return $r;
    }

    protected function opDialog()
    {
        echo 'r';
    }
}
