<?php

class jqReceipts extends jqGrid
{
	protected function init()
	{
		global $core, $lang;
		$this->options = array(
			'sortname' => 'id',
			'sortorder' => 'desc',
		);
		$this->nav = array(
			#Set common excel export
			'excel' => true,
			'exceltext' => 'Excel',
			'trash' => true,
			'trashtext' => 'Показать удаленные',
			'trashfield' => 'r.deleted_at',
			'trashtype' => 'null',
		);
		$this->do_search = true;
		$this->multipleSearch = true;
		$uid_types = $this->getUidTypes();
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$del = $this->getDel();
		$del_select = $this->getDel(true);
		$org_types = $this->getOrgTypes();
		$this->query = "SELECT {fields} FROM " . DB_PREFIX . "_receipts as r LEFT JOIN " . DB_PREFIX . "_object as o ON(r.object = o.id) LEFT JOIN JMY_org as org on(org.id=r.org) LEFT JOIN " . DB_PREFIX . "_sales as s ON(s.id = r.sales_id) LEFT JOIN " . DB_PREFIX . "_supply_order as so ON(so.id = r.supply_id) WHERE r.status <> '0' AND {where} ";
		$this->cols = array(
			'id' => array(
				'hidden' => true,
				'db' => 'r.id',
			),
			'oid_real' => array(
				'hidden' => true,
				'db' => 'r.oid',
			),
			'org_real' => array(
				'hidden' => true,
				'db' => 'r.org',
			),
			'active_real' => array(
				'hidden' => true,
				'db' => 'r.active',
			),
			'object_id' => array(
				'hidden' => true,
				'db' => 'r.object',
			),
			'sales_real' => array(
				'hidden' => true,
				'db' => 's.id',
			),
			'code' => array(
				'db' => 'r.id',
				'label' => 'Код',
				'width' => 5,
				'hidden' => false,
				'search_op' => 'like'
			),

			'numb' => array(
				'db' => 'r.numb',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),
			'numb_in' => array(
				'label' => $lang['supply_receipt_doc_number'],
				'width' => 20,
				'db' => 'r.doc_number',
				'hidden' => false,
				'search_op' => 'like'
			),
			'supply_id' => array(
				'hidden' => true,
				'db' => 'r.supply_id',
			),
			'supply_id_numb' => array(
				'label' => $lang['supply'],
				'width' => 10,
				'db' => 'so.numb',
				'encode' => false,
				'hidden' => false,
				'search_op' => 'like'
			),
			'sales_id' => array(
				'label' => $lang['sales'],
				'width' => 10,
				'db' => 's.numb',
				'encode' => false,
				'hidden' => false,
				'null' => $lang['not_specified'],
				'search_op' => 'like'
			),

			'total' => array(
				'label' => 'Сумма',
				'hidden' => false,
				'db' => 'r.total',
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 20,
				'excel_hidden' => true,
			),

			'total_excel' => array(
				'label' => 'Сумма',
				'hidden' => true,
				'db' => 'r.total',
				'null' => '0',
				'encode' => false,
				'width' => 20,
				'excel_only' => true,
			),
			'oid' => array(
				'label' => $lang['oid'],
				'hidden' => false,
				'db' => 'r.oid_name',
				'encode' => false,
				'null' => $lang['not_specified'],
				'width' => 30,
			),
			'object' => array(
				'label' => 'Объект',
				'hidden' => false,
				'db' => 'o.name',
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,
			),
			'uid' => array(
				'label' => $lang['uid'],
				'db' => 'r.uid',
				'width' => 15,
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

			'org' => array(
				'label' => 'Организация',
				'db' => 'org.id',
				'width' => 25,
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $org_types,
				'stype' => 'select',
				'searchoptions' => array(
					'value' => new jqGrid_Data_Value($org_types, 'Все'),
				),
				'search_op' => 'equal',
			),

			'date_receipts' => array(
				'label' => $lang['date'],
				'db' => 'r.date_receipts',
				'width' => 15,
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
			'date' => array(
				'label' => "Дата создания",
				'db' => 'r.date',
				'width' => 15,
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



			// 'del' => array(			
			// 	'label' => "Архив",
			//     'db' => 'r.del',
			//     'width' => 9,              
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'replace' => $del,
			//     'stype' => 'select',
			//     'searchoptions' => array(
			//         'defaultValue' => '0',
			//         'value' => new jqGrid_Data_Value($del_select, $lang['all']),				
			//     ),
			//     'search_op' => 'equal',
			// ), 	

			'status' => array(
				'label' => $lang['active'],
				'width' => 9,
				'align' => 'center',
				'db' => 'r.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
				'stype' => 'select',
				'searchoptions' => array(
					'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
				),
				'search_op' => 'equal',
				'excel_hidden' => true,
			),

			'status_excel' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',
				'hidden' => true,
				'db' => 'r.active',
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_select,
				'stype' => 'select',
				'searchoptions' => array(
					'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
				),
				'search_op' => 'equal',
				'excel_only' => true,

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
				'excel_hidden' => true,
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

			if ($start > $end) {
				list($start, $end) = array($end, $start);
			}

			$start = date('Y-m-d', $start);
			$end = strtotime("+1 day", $end);
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

	protected function getAutoTypes($select = false)
	{
		global $lang;
		$rows_select = array();
		$rows_select[0] = $lang['active_off'];
		$rows_select[1] = $lang['active_on'];

		$rows = array();
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-check-square text-danger fa-md' data-toggle='tooltip' title='" . $lang['supply_receipt_active_off'] . "'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-check-square text-success fa-md' data-toggle='tooltip' title='" . $lang['supply_receipt_active_on'] . "'></span>";

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

	protected function getUidTypes()
	{
		global $lang;
		$result = $this->DB->query("SELECT * FROM JMY_receipts as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0' ORDER BY u.surname");
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

	protected function getOrgTypes()
	{
		$result = $this->DB->query("SELECT r.id, o.name FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)");
		$rows = array();
		while ($r = $this->DB->fetch($result)) {
			$rows[$r['id']] = $r['name'];
		}
		return $rows;
	}

	protected function parseRow($r)
	{
		global $core, $db, $lang;
		//$r['numb'] = module_perfix('receipts', $r['id'], $r['org_real']);
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="' . ADMIN . '/organizations/edit/' . $r['oid_real'] . '" target="_blank">' . ee($r['oid']) . '</a>';
		$r['supply_id_numb'] = empty($r['supply_id_numb']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="' . ADMIN . '/module/supply/order_detail/' . $r['supply_id'] . '" target="_blank">' . $r['supply_id_numb'] . '</a>';
		$r['sales_id'] = empty($r['sales_id']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : '<a href="' . ADMIN . '/module/sales/order/' . $r['sales_real'] . '" target="_blank">' . $r['sales_id'] . '</a>';
		$query = $db->query("SELECT * FROM JMY_attach WHERE `pid` = '" . $r['id'] . "' AND `module` = 'receipts'");
		if ($db->numRows($query) > 0) {
			$modal_doc_content = '<ul>';
			while ($att = $db->getRow($query)) {
				$modal_doc_content .= '<li><a target="_black" href="' . $att["url"] . '">' . $att["name"] . '</a></li>';
			}
			$modal_doc_content .= '<ul>';
		} else {
			$modal_doc_content = $lang['supply_receipt_document_empty'];
		}



		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \'' . ADMIN . '/module/supply/receipts_detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">' . $lang['view'] . '</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-active-' . $r['id'] . '\')">' . ($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']) . '</a></li>  
							<li><a href="javascript:void(0)" onclick="print_modal(' . $r['id'] . ');">Распечатать поступление</a></li>
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-doc-' . $r['id'] . '\')">' . $lang['supply_receipt_document'] . '</a></li>  
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-' . $r['id'] . '`).modal(`show`);">Отправить на синхронизацию</a></li> 
							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="removeReceipts(this)" data-status="' . $r["status"] . '" data-id="' . $r['id'] . '" data-view-id="' . module_perfix('receipts', $r['id']) . '">' . $lang['delete'] . '</a></li>
						</ul>
						<div id="modal-active-' . $r['id'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">' . $lang['confirm'] . '</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . str_replace('[receipts]', module_perfix('receipts', $r['id']), ($r['active_real'] == 0 ? $lang['supply_receipt_active_on_title'] : $lang['supply_receipt_active_off_title'])) . '</h3>
								<hr class="short alt">
								<p>' . str_replace('[receipts]', module_perfix('receipts', $r['id']), ($r['active_real'] == 0 ? $lang['supply_receipt_active_on_text'] : $lang['supply_receipt_active_off_text'])) . '</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" data-id="' . $r['id'] . '" class="btn btn-' . ($r['active_real'] == 0 ? 'success' : 'warning') . ' receiptsBtn">' . ($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']) . '</button>
							  </div>
							</div>
						</div>
						<div id="modal-doc-' . $r['id'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">' . $lang['confirm'] . '</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . $lang['supply_receipt_document'] . '</h3>
								<hr class="short alt">
								<p>' . $modal_doc_content . '</p>
							  </div>							  
							</div>
						</div>
						<div id="modal-print-' . $r['id'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="far fa-check-square"></i></span><span class="panel-title">' . $lang['confirm'] . '</span></div>
							  <div class="panel-body">
								<h3 class="mt5">' . $lang['supply_receipt_document'] . '</h3>
								<hr class="short alt">
								<p>' . $modal_doc_content . '</p>
							  </div>							  
							</div>
						</div>
					</div>
					<div class="modal fade" id="syncModal-' . $r['id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" style="color: #000;" id="exampleModalLabel">Отправить на синхронизацию</h5>       
						  </div>
						  <div class="modal-body" style="color: #000;">
							Вы действительно хотите отправить данный документ поступления на синхронизацию с 1С Бухгалтерией? 
						  </div>
						  <div class="modal-footer">
							<button type="button" style="color: #fff;" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
							<button type="button" style="color: #fff;" class="btn btn-primary syncBtn" data-id="' . $r['id'] . '">Отправить</button>
						  </div>
						</div>
					  </div>
					</div>      
					<div class="btn-group">
						<button type="button" onclick="' . disabled_all() . ' modal_o(\'#modal-active-' . $r['id'] . '\')" class="btn btn-xs btn-' . ($r['active_real'] == 0 ? 'success' : 'warning') . '"><span style="    font-size: 14px;" class="far fa-check-square" data-toggle="tooltip" title="' . ($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']) . '"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="print_modal(' . $r['id'] . ');" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>';
		$r['total'] = empty($r['total']) ? '<font color="red">' . $lang['not_specified'] . '</font>' : number_format($r['total'], 2, '.', ' ') . ' руб.';
		if (is_date($r["date_receipts"], "Y-m-d")) $r["date_receipts"] = date("d.m.Y", strtotime($r["date_receipts"]));
		if (is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y", strtotime($r["date"]));
		return $r;
	}

	protected function opDialog()
	{
		echo 'r';
	}
}
