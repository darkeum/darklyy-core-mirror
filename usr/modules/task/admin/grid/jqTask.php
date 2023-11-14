<?php

class jqTask extends jqGrid
{
    protected function init()
    {
        global $taskAttr, $lang;

        //$this->nav = array('excel' => true);
        //$auto_types = $this->getAutoTypes();
        $fromid = $this->getUidTypes('fromid');
        $toid = $this->getUidTypes('toid');
        $status_type = $this->getStatusType();
        $status_value = $this->getStatus();
        //$pay_types = $this->getPayTypes();
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'DESC',
        );
        $this->query = $taskAttr['query'];
        $this->cols = array(


            #Real id is hidden
            'id' => array('hidden' => true,
                'db' => 't.id',
            ),

            'object_id' => array('hidden' => true,
                'db' => 't.object',
            ),

            /*
			'status_real' => array('hidden' => true,
                'db' => 'v.status',
            ),
            */
            'numb' => array(
                'db' => 't.id',
                'label' => '№',
                'width' => 10,
                'hidden' => false,
                'manual' => true,
                'search_op' => 'like'
            ),

            'type' => array(
                'label' => '<span class="octicon octicon-alignment-unalign text-dark octicon-md" data-toggle="tooltip" title="' . $lang['task_type'] . '"></span>',
                'width' => 10,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),

            'title' => array(
                'db' => 't.title',
                'label' => 'Заголовок',
                'width' => 100,
                'hidden' => false,
                'manual' => false,
                'encode' => true,
                'sortable' => true,
                'search_op' => 'like',
                'search' => true,
                'title' => true,
                'null' => 'Не указан',
            ),

            'fromid' => array(
                'label' => 'Отправитель',
                'db' => 't.fromid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $fromid,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($fromid, 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'toid' => array(
                'label' => 'Получатель',
                'db' => 't.toid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $toid,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($toid, 'Все'),
                ),
                'search_op' => 'equal',
            ),


            'object' => array(
                'label' => 'Объект',
                'hidden' => false,
                'db' => 'obj.name',

                'null' => 'Не указано',
                'encode' => false,
                'width' => 40,
            ),

            'date' => array(
                'label' => 'Начало',
                'width' => 40,
                'db' => 't.date',
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
                        array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',
                    ),
                ))),
                'search_op' => 'date_range',
            ),

            'date_end' => array(
                'label' => 'Окончание',
                'width' => 40,
                'db' => '(SELECT (CASE WHEN (t.endsw = 0) THEN \'Без учета времени\' ELSE t.date_end END))',
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
                        array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',
                    ),
                ))),
                'search_op' => 'date_range',
            ),

            /*
			'bc' => array(
				'label' => 'Статус',
				'width' => 15,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
            */
            'status' => array(
                'label' => 'Статус',
                'db' => 't.status',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'replace' => $status_type,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_value, 'Все'),
                ),
                'search_op' => 'equal',
            ),
            'ab' => array(
                'label' => 'Действия',
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

    protected function searchOpDateRange($c, $val)
    {
        //--------------
        // Date range
        //--------------

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

        //------------
        // Single date
        //------------

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

    protected function getUidTypes($res)
    {
        global $core, $taskAttr;
        switch ($res) {
            case 'fromid':
                $result = $this->DB->query("SELECT u.* FROM JMY_task as r LEFT JOIN JMY_users as u on (r.fromid=u.id) WHERE " . $taskAttr['uid'] . " ORDER BY u.surname");
                break;
            case 'toid':
                $result = $this->DB->query("SELECT u.* FROM JMY_task as r LEFT JOIN JMY_users as u on (r.toid=u.id) WHERE " . $taskAttr['uid'] . " ORDER BY u.surname");
                break;
        }

        $rows = array();
        $rows[0] = 'Не указан';
        while ($r = $this->DB->fetch($result)) {
            $rows[$r['id']] = shortUserName($r['id']);
        }
        return $rows;
    }

    protected function getUidTypes2($res)
    {
        global $core;
        $result = $this->DB->query("SELECT * FROM JMY_task as r LEFT JOIN JMY_users as u on (r." . $res . "=u.id) WHERE r.toid=" . $core->auth->user_info['id'] . " ORDER BY u.surname");

        $rows = array();
        $rows[0] = 'Не указан';
        while ($r = $this->DB->fetch($result)) {
            $result_row = $r['surname'] . ' ';
            $result_row .= mb_substr($r['name'], 0, 1) . '. ';
            $result_row .= mb_substr($r['ochestvo'], 0, 1) . '.';
            $rows[$r['id']] = $result_row;
        }
        return $rows;
    }

    protected function getStatusType()
    {
        global $db, $lang, $core;
        $rows = [];
        $rows[1] = "<span class='far fa-circle text-warning fa-md' data-toggle='tooltip' title='" . $lang['task_do'] . "'></span>";
        $rows[2] = "<span class='fa fa-check-circle text-warning fa-md' data-toggle='tooltip' title='" . $lang['task_checked'] . "'></span>";
        $rows[3] = "<span class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='" . $lang['task_ok'] . "'></span>";
        $rows[4] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Отправлена на доработку'></span>";
        $rows[7] = "<span class='fas fa-ban text-danger fa-md' data-toggle='tooltip' title='Задача отменена'></span>";
        return $rows;
    }

    protected function getStatus()
    {
        $rows[1] = "Выполняется";
        $rows[2] = "Отправлена на проверку";
        $rows[3] = "Выполнено";
        $rows[4] = "Отправлена на доработку";
        $rows[7] = "Задача отменена";
        return $rows;
    }

    protected function parseRow($r)
    {
        global $org_array, $lang, $db;
        global $adminTpl, $core, $admin_conf, $lang;
        $r['numb'] = $r['id'];
        $r['object'] = empty($r['object_id']) ? '<font color="red">Не указано</font>' : '<a href="' . ADMIN . '/object/view/' . $r['object_id'] . '" target="_blank">' . $r['object'] . '</a>';
        if (($core->auth->user_info['id'] != $r['toid']) && ($core->auth->user_info['id'] == $r['fromid'])) {
            $arrows = ' <span class="octicon octicon-alignment-aligned-to text-success fa-md" data-toggle="tooltip" title="Поставленная вами задача"></span>';
        } elseif (($core->auth->user_info['id'] == $r['toid']) && ($core->auth->user_info['id'] != $r['fromid'])) {
            $arrows = ' <span class="octicon octicon-alignment-align text-info fa-md" data-toggle="tooltip" title="Поставленная вам задача"></span>';
        } else {
            $arrows = ' <span class="octicon octicon-alignment-unalign text-success fa-md" data-toggle="tooltip" title="Личная задача"></span>';
        }

        $r['type'] = $arrows;

        $r['ab'] = '<div class="btn-group">
								<button type="button" onclick="location.href = \'/administration/module/task/view/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Открыть</button>
								<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
								<ul role="menu" class="dropdown-menu">
									<li><a href="javascript:void(0);" onclick="archiveModalFunc(' . $r["id"] . ');">' . $lang['task_inarchive'] . '</a></li>
								</ul>
							</div>';//modalConstract($r['id'], $r['fromid'], $r['toid'], $r['status'], true, true, true);
        //$r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 1, $r['status']);
        //$r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 2, $r['status']);
        $r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 3, $r['status']);

        if (is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if (is_date($r["date_end"], "Y-m-d H:i:s")) $r["date_end"] = date("d.m.Y H:i:s", strtotime($r["date_end"]));
        return $r;
    }

    protected function opDialog()
    {
        echo 'r';
    }
}