<?php

namespace Sales2\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected $opts_type = "open";

    protected function getOpenUrl()
    {
        return route("admin.sales.show", ["id" => "@id"]);
    }

    protected function init()
    {
        $this->title = 'Заказы';

        $this->options = array(
            'sortname' => 's.id',
            'sortorder' => 'DESC',
        );

        $this->nav = array(
            'excel' => true,
            'exceltext' => 'Сохранить в Excel',
            // 'trash' => true,
            // 'trashtype' => 'null',
            // 'trashtext' => 'Показывать удаленные',
            // 'trashfield' => 'r.deleted_at',
        );

        $this->db = DB::table('sales as s')
            ->leftJoin("organizations as o", "o.id", "=", "s.oid")
            ->leftJoin("object as obj", "obj.id", "=", "s.object")
            ->where("s.del", 0)
            ->where("s.is_clone", 0);

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 's.id',
            ),
            'hidden_site' => array(
                'hidden' => true,
                'db' => 's.hidden_site',
            ),
            'status_real' => array(
                'hidden' => true,
                'db' => 's.status',
            ),
            'confirm_supply_order' => array(
                'hidden' => true,
                'db' => 's.confirm_supply_order',
            ),

            'oid_real' => array(
                'hidden' => true,
                'db' => 's.oid',
            ),

            'object_id' => array(
                'hidden' => true,
                'db' => 's.object',
            ),
            'end_sales' => array(
                'hidden' => true,
                'db' => 's.end',
            ),

            'code' => array(
                'db' => 's.id',
                'label' => 'Код',
                'width' => 10,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'numb' => array(
                'db' => 's.numb',
                'label' => '№',
                'width' => 15,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => 's.title',
                'label' => 'Наименование',
                'width' => 35,
                'encode' => false,
                'hidden' => false,
                'null' => 'Не указан',
                'search_op' => 'like'
            ),

            'oid' => array(
                'label' => 'Контрагент',
                'hidden' => false,
                'db' => 'o.name',
                'encode' => false,
                'null' => 'Не указан',
                'width' => 35,
            ),

            'object' => array(
                'label' => 'Объект',
                'hidden' => false,
                'db' => 'obj.name',
                'null' => 'Не указано',
                'encode' => false,
                'width' => 40,
            ),

            'total' => array(
                'label' => 'Сумма',
                'hidden' => false,
                'db' => 's.total',
                'null' => "Не указано",
                'encode' => false,
                'width' => 25,
                'excel_hidden' => true,
            ),

            'total_re' => array(
                'label' => 'Оставшееся сумма реализаций',
                'width' => 25,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
                'excel_hidden' => true,
            ),

            'total_re_excel' => array(
                'label' => 'Оставшееся сумма реализаций',
                'hidden' => true,
                'manual' => true,
                'encode' => false,
                'width' => 30,
                'excel_only' => true,
            ),

            'datetime' => array(
                'hidden' => true,
                'db' => 's.date',
            ),

            'date' => array(
                'label' => 'Дата',
                'db' => 's.date',
                'width' => 15,
                'format_date' => "d.m.Y",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'org' => array(
                'label' => 'Организация',
                'null' => 'Не указано',
                'db' => 's.org',
                'width' => 20,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getOrg("sales"),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getOrg("sales"), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'uid' => array(
                'label' => 'Ответственный',
                'db' => 's.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => 'Не указан',
                'sortable' => false,
                'replace' => $this->getUid('sales'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('sales'), 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'total_excel' => array(
                'label' => 'Сумма',
                'hidden' => true,
                'db' => 'total',
                'null' => '0',
                'encode' => false,
                'width' => 30,
                'excel_only' => true,
            ),

            'status_client' => array(
                'label' => "Статус клиента",
                'width' => 15,
                'align' => 'center',
                'db' => 's.status_client',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getStatusClient(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatusClient(true), 'Все'),
                    // 'dataInit' => $this->initMultiselect(),

                    // 'multiple'=>true,
                ),
                'search_op' => 'auto',
                'excel_hidden' => true,
            ),

            'end' => array(
                'label' => "Закрытие",
                'width' => 15,
                'align' => 'center',
                'db' => 's.end',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getAutoTypesEnd(),
                'stype' => 'select',
                'searchoptions' => array(
                    // 'defaultValue' => '0',
                    'value' => $this->setValue($this->getAutoTypesEnd(true), 'Все'),
                ),
                'search_op' => 'equal',
                'excel_hidden' => true,
            ),

            'sales_type' => array(
                'label' => "Тип",
                'width' => 15,
                'align' => 'center',
                'db' => 's.sales_type',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getType(),
                'stype' => 'select',
                'searchoptions' => array(
                    // 'defaultValue' => '0',
                    'value' => $this->setValue($this->getType(true), 'Все'),
                ),
                'search_op' => 'equal',
                'excel_hidden' => true,
            ),

            'sales_type_excel' => array(
                'label' => 'Тип',
                'hidden' => true,
                'db' => 's.sales_type',
                'encode' => false,
                'width' => 30,
                'excel_only' => true,
            ),

            'end_excel' => array(
                'label' => "Закрытие",
                'width' => 15,
                'align' => 'center',
                'hidden' => true,
                'db' => 's.end',
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getAutoTypesEnd(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getAutoTypesEnd(), "Все"),
                ),
                'search_op' => 'equal',
                'excel_only' => true,

            ),

            'end_date' => array(
                'label' => 'Дата закрытия',
                'width' => 15,
                'db' => 's.end_date',
                'null' => 'Не указано',
                'format_date' => "d.m.Y",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),


            'pay' => array(
                'label' => 'Оплата',
                'width' => 15,
                'db' => 's.pay',
                'align' => 'center',
                'null' => 'Не указано',
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                // 'search' => false,
                'replace' => $this->getPayTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getPayTypes(true), 'Все'),
                ),
                'search_op' => 'equal',
                // 'search_op' => false,
                'excel_hidden' => true,
            ),

            // 'pay_excel' => array(
            // 	'label' => 'Оплата',
            // 	'width' => 15,
            // 	'db' => 's.pay',
            // 	'align' => 'center',
            // 	'null' => 'Не указано',
            // 	'hidden' => true,
            // 	'encode' => false,
            // 	'sortable' => false,
            // 	'replace' => $this->getPayTypes(),
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => $this->setValue($this->getPayTypes(), 'Все'),
            //     ),
            //     'search_op' => 'equal',	
            // 	'excel_only' => true,
            // ),	

            'task_id' => array(
                'hidden' => true,
                'db' => 's.task_id',
            ),

            'task_availability' => array(
                'hidden' => true,
                'db' => 's.task_availability',
            ),

            'task_title' => array(
                'label' => 'Текст задачи',
                'hidden' => false,
                'db' => 's.task_title',
                'encode' => false,
                'width' => 40,
            ),

            'task_date_end' => array(
                'label' => 'Дата окончания задачи',
                'db' => 's.task_date_end',
                'width' => 25,
                'null' => 'Не указано',
                'format_date' => "d.m.Y",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'source_appeal' => array(
                'label' => 'Источник обращения',
                'hidden' => true,
                'db' => 's.source_appeal',
                'encode' => false,
                'width' => 30,
                'excel_only' => true,
            ),
            'market_segment' => array(
                'label' => 'Сегмент рынка',
                'hidden' => true,
                'db' => 's.market_segment',
                'encode' => false,
                'width' => 30,
                'excel_only' => true,
            ),

            'ab' => array(
                'label' => 'Выберите действие',
                'width' => 20,
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

    protected function getStatusClient($type = false)
    {
        $rows_select = \Sales\Model\Sales::getStatusClient();

        $rows = [];
        $rows[1] = "<span class='fas fa-user-plus text-primary fa-md' data-toggle='tooltip' title='{$rows_select[1]}'></span>";
        $rows[2] = "<span class='fas fa-user-tie text-warning fa-md' data-toggle='tooltip' title='{$rows_select[2]}'></span>";
        $rows[3] = "<span class='fas fa-user-tie text-success fa-md' data-toggle='tooltip' title='{$rows_select[3]}'></span>";
        $rows[4] = "<span class='fas fa-user-check text-success fa-md' data-toggle='tooltip' title='{$rows_select[4]}'></span>";
        $rows[5] = "<span class='fas fa-truck text-warning text-success fa-md' data-toggle='tooltip' title='{$rows_select[5]}'></span>";
        $rows[6] = "<span class='fas fa-truck text-success text-success fa-md' data-toggle='tooltip' title='{$rows_select[6]}'></span>";
        $rows[7] = "<span class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='{$rows_select[7]}'></span>";

        return $type == 'select' ? $rows_select : $rows;
    }

    protected function getAutoTypesEnd($select = false)
    {
        $rows_select = [];
        $rows = [];

        $rows[0] = 'К исполнению';
        // $rows[3] = 'В работе';
        $rows[3] = 'К отгрузке';
        $rows[1] = 'Завершена успешно';
        $rows[2] = 'Завершена не успешно';

        $rows_select[0] = "<span class='far fa-circle text-warning' title='" . $rows[0] . "'></span>";
        $rows_select[3] = "<span class='far fa-circle text-primary' title='" . $rows[3] . "'></span>";
        $rows_select[1] = "<span class='far fa-circle text-success' title='" . $rows[1] . "'></span>";
        $rows_select[2] = "<span class='far fa-circle text-danger' title='" . $rows[2] . "'></span>";

        return ($select ? $rows : $rows_select);
    }

    protected function getType($select = false)
    {
        $rows_select = \Sales\Model\Sales::getSalesType();
        $rows = array();

        $rows[0] = "<span class='fas fa-text text-secondary fa-md' data-toggle='tooltip' title='" . $rows_select[0] . "'></span>";
        $rows[1] = "<span class='fas fa-shopping-basket text-primary fa-md' data-toggle='tooltip' title='" . $rows_select[1] . "'></span>";
        $rows[2] = "<span class='fas fa-cogs text-alert fa-md' data-toggle='tooltip' title='" . $rows_select[2] . "'></span>";
        $rows[3] = "<span class='fas fa-money-check-alt text-warning fa-md' data-toggle='tooltip' title='" . $rows_select[3] . "'></span>";

        $rows[4] = "<span class='fas fa-money-check-alt text-warning fa-md' data-toggle='tooltip' title='" . $rows_select[4] . "'></span>";
        $rows[5] = "<span class='fas fa-money-check-alt text-warning fa-md' data-toggle='tooltip' title='" . $rows_select[5] . "'></span>";

        return ($select ? $rows_select : $rows);
    }

    protected function getPayTypes($select = false)
    {
        $rows_select = array();
        $rows_select[0] = 'Не оплачена';
        $rows_select[1] = 'Частично оплачена';
        $rows_select[2] = 'Оплачена';

        $rows = array();
        $rows[0] = "<span style='margin-left: -12px;' class='fa fa-rub text-danger fa-md' data-toggle='tooltip' title='{$rows_select[0]}'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='fa fa-rub text-warning fa-md' data-toggle='tooltip' title='{$rows_select[1]}'></span>";
        $rows[2] = "<span style='margin-left: -12px;' class='fa fa-rub text-success fa-md' data-toggle='tooltip' title='{$rows_select[2]}'></span>";

        return ($select ? $rows_select : $rows);
    }

    protected function parseRow($r)
    {

        $r['oid'] = empty($r['oid_real']) ? 'Не указано' : '<a href="' . router("admin.organizations.show", $r['oid_real']) . '" target="_blank">' . $r['oid'] . '</a>';
        $r['object'] = empty($r['object_id']) ? '<font color="red">Не указано</font>' : '<a href="/' . ADMIN . '/object/view/' . $r['object_id'] . '" target="_blank">' . $r['object'] . '</a>';
        $r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
        $hidden_site_text = $r["hidden_site"] == 0 ? 'Скрыть для покупателя' : 'Открыть для покупателя';


        $show_btn = router("admin.sales.show", $r['id']);
        $hidden_site = $r["hidden_site"] == 0 ? 1 : 0;
        $r['ab'] = <<<HTML
            <div class="btn-group">
				<a href="{$show_btn}" target="_blank" class="btn btn-sm btn-primary" title="Просмотр"><i class="fas fa-eye"></i></a>
						<button type="button" data-toggle="dropdown" class="btn btn-sm btn-primary dropdown-toggle"></button>
						<ul role="menu" class="dropdown-menu pull-right">		
							<li><a href="javascript:void(0)" onclick="modal_print('{$r['id']}');">Распечатать продажу</a></li>  
							<li><a href="javascript:void(0)" onclick="window.open(\'/catalog_v2/specification/sales/{$r['id']}\',\'_blank\'); return false;">Получить спецификацию</a></li>
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-{$r['id']}`).modal(`show`);">Отправить на синхронизацию</a></li> 
							<li style="display:none;"><a href="javascript:void(0)" onclick="hidden_site_sales({
                                id:'{$r['id']}', 
                                status:'{$hidden_site}', 
                                status_text:'{$hidden_site_text}'
                            });">{$hidden_site_text}</a></li> 

							<li><a href="javascript:void(0)" onclick="showFastTaskModal({$r['id']});">Быстрая задача</a></li> 

							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="del_sales({$r['id']});">Удалить</a></li> 
						</ul>
						  
						  <div class="modal fade" id="syncModal-{$r['id']}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog" role="document">
								<div class="modal-content">
								  <div class="modal-header">
									<h5 class="modal-title" style="color: #000;" id="exampleModalLabel">Отправить на синхронизацию</h5>       
								  </div>
								  <div class="modal-body" style="color: #000;">
									Вы действительно хотите отправить данную реализацию на синхронизацию с 1С Бухгалтерией? 
								  </div>
								  <div class="modal-footer">
									<button type="button" style="color: #fff;" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
									<button type="button" style="color: #fff;" class="btn btn-primary syncBtn" data-id="{$r['id']}">Отправить</button>
								  </div>
								</div>
							  </div>
							</div>    
						  
					</div>
HTML;

        // $datetime = date("d.m.Y H:i:s", strtotime($r["datetime"]));
        // if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = "<div title='{$datetime}'>".date("d.m.Y", strtotime($r["date"]))."</div>";
        // if(is_date($r["task_date_end"], "Y-m-d H:i:s")) $r["task_date_end"] = date("d.m.Y H:i:s", strtotime($r["task_date_end"]));

        $total = $r['total'];
        $r['total'] = empty($total) ? 'Не указано' : number_format($total, 2, '.', ' ') . ' руб.';

        $cols_class = "";
        if ($r["confirm_supply_order"] !== '[]') {
            $cols_class = "red";
        }
        $r['_class'] = $cols_class;

        $total_re_name = "sales_total_re_{$r['id']}";
        if (\Boot\Support\Cache::has($total_re_name)) {
            $total_re = \Boot\Support\Cache::get($total_re_name);
        } else {
            $total_re = \Realization\Model\Realization::where("sales_id", $r["id"])->where("active", 1)->sum("total");
            \Boot\Support\Cache::set($total_re_name, $total_re, 10800);
        }
        $total_re_ = $total - $total_re;
        $r["total_re"] = number_format($total_re_, 2, '.', ' ') . ' руб.';
        $r["total_re_excel"] = $total_re_;

        $task_title = empty($r["task_id"]) ? ($r["task_availability"] == 0 ? "Не указано" : "Все задачи выполнены") : $r["task_title"];
        $r["task_title"] = "<div onclick='showViewTaskModal({$r['id']})' style='cursor:pointer;'>{$task_title}</div>";

        $sales_type_list = \Sales\Model\Sales::getSalesType();
        $r["sales_type_excel"] = $sales_type_list[$r["sales_type_excel"]] ?? "";

        $source_appeal = \Sales\Model\Sales::getSourceAppeal();
        $r["source_appeal"] = $source_appeal[$r["source_appeal"]] ?? "Не указано";
        $market_segment = \Sales\Model\Sales::getMarketSegment();
        $r["market_segment"] = $market_segment[$r["market_segment"]] ?? "Не указано";

        return $r;
    }
}
