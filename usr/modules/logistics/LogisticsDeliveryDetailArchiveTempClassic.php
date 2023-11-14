<?php

namespace Logistics;

class LogisticsDeliveryDetailArchiveTempClassic{
	public function __construct(){
		$this->lddt = new \Logistics\LogisticsDeliveryDetailTemp;
	}


    static public function grid(){
        global $adminTpl;
        $lnn = "Архив";
		// echo <<<HTML

		// 	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js">
		// 	</script>

		// HTML;
	    $adminTpl->admin_head();
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = 'logistics/delivery_archive';
	    $atributs['module_open'] = 'view';
	    loadGrid('jqLogisticsDeliveryArchive', $lnn, $atributs, ROOT . '/usr/modules/logistics/admin/grid/');
	    $adminTpl->footIncludes[] = '<script src="/usr/modules/logistics/admin/asset/js/pop.js"></script>';
	    $adminTpl->admin_foot();
    }

	public function view(){
		global $adminTpl;
		$adminTpl->admin_head();
		echo <<<HTML
			<section id="content" class="">
    		    <h3 class="text-center">
    		        
    		    </h3>
    		    <div class="row" id="admin-form">
    		        <div class="panel mb25">
    		            <div class="panel-body p20 pb10">
    		                <div class="section row mbn admin-form">
    		                    <div class="col-md-12 pl15">
    		                        <div class="section row mb15" id="con_header">{$this->view_head()}</div>
    		                    </div>
    		                </div>
    		            </div>
    		        </div>
    		    </div>

    		    <div class="row">
					<div class="panel panel-dark panel-border top">
						<div class="panel-heading">
							<span class="panel-title">Список товаров:</span>
							<div class="widget-menu pull-right" >
    		                    						
							</div>	

    		          </div>

    		          <div class="panel-body pn table-responsive"> 
						<form id="tablesForm" method="POST" onsubmit="return false;">
    		              <table class="table table-striped">
    		                <thead id="table_thead">
    		                    <tr>
				                	<th class="w40"><span class="pd-l-sm"></span>№</th>
				                	<th class="w200">Наименование</th>
				                	<th class="w150 sku-units" data-type="1"><span>Артикул</span></th>
				                	<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>	
				                	<th class="w100">Сумма</th>
				                	<th class="w20 text-right">
				                		<div class="checkbox-custom mb15">
				                			<input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); return true;" class="checks">
				                			<label for="all"></label>
				                		</div>	
				                	</th>
				                </tr>
    		                </thead>
    		                <tbody id="tov_list">{$this->view_body()}</tbody>
						<tfoot class="footer-menu">
    		                <tr align="right">                    
							  <td align="right" colspan="15">
    		                    <nav align="right" class="text-right">						
									<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act"></select>
									<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" disabled onclick="subAct();"><span class="pd-l-sm"></span>	
								 </nav>
    		                  </td>					 
    		                </tr>
    		              </tfoot> 
						</table> 
						</form>
					 </div>			
    		      </div>
    		    </div>
			</section>
			<script>
				$(function(){
					doc_number_editable();
});

				  function doc_number_editable(){
        const pk = $("#mainId").val();
        $('#delivery_price_view').editable({  
            type: "text",
            pk,
            emptytext: 'Отсутствует',
            url: "/ajax.php?do=changeLogisticFastSum"
        });
    }
				</script>
HTML;
		echo \Logistics\LogisticsDeliveryDetailTemp::ttn_modal();
		\Logistics\LogisticsTemp::script();
		$adminTpl->admin_foot();
	}

	public function get_data(){
		if(!isset($this->data)) $this->data = $this->get_logistics_delivery();
	}

	public function get_logistics_delivery(){
		global $db;
		$query = $db->query("SELECT ld.* FROM `".DB_PREFIX."_logistics_delivery` as ld WHERE ld.id = '{$this->id}'");
		$data = $db->getRow($query);
		$LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
		$list_items_module = $LogisticsDeliveryDetailTemp->list_items_module($data);
		foreach($list_items_module as $field => $value){
			$data[ $field ] = $value;
		}
		return $data;
	}

	public function view_head(){
		$this->get_data();
        $delivery_type_list = \Logistics\Model\LogisticsDelivery::getDeliveryType();

		$item = $this->data;
    
        $id = $item["id"];

		$ttn_print_button = \Logistics\LogisticsDeliveryDetailTemp::ttn_button($item, $id);

        $link = $this->lddt->head_link($item);
        $div_o_name = $this->lddt->head_div_name($item);
        $org_name = $item["org_name"];
        $contact_person = $item["contact_person"];
        $phone = $item["phone"];
        $delivery_type = $item["delivery_type"];
        $delivery_price = $item["delivery_price"];
        $delivery_price_view = $this->lddt->lvp($delivery_price);
        $delivery_type_item = $delivery_type_list[ $delivery_type ];
        $comment = empty($item["comment"]) ? "Не указан" : nl2br($item["comment"]);
        $delivery_date = date("d.m.Y", strtotime($item["delivery_date"]));
        $delivery_from = date("H:i", strtotime($item["delivery_from"]));
        $delivery_to = date("H:i", strtotime($item["delivery_to"]));

        $address_temp = $this->lddt->head_address_temp($item);
		return <<<HTML
			<div style="flex: 1;">
                <div class="col-sm-4 col-xs-12">
                    {$div_o_name}
                    <div class="mt-20">Дата: {$delivery_date}</div>
                    <div class="mt-20">С: {$delivery_from} до {$delivery_to}</div>
                </div>
                <div class="col-sm-4 col-xs-12 min_view">
                    <div>{$address_temp}</div>
                    <div class="mt-20">Контактное лицо: {$contact_person}</div>
                    <div class="mt-20">Телефон: <a href="tel:{$phone}">{$phone}</a></div>
                </div>
                <div class="col-sm-4 col-xs-12 min_view">
                    <div>{$link}</div>
                    <div class="mt-20">Доставка: {$delivery_type_item} (<b id="delivery_price_view" class="cp">{$delivery_price}</b> руб.)</div>
					<input type="hidden" name="mainId" id="mainId" value="{$item['id']}" />
                    <div class="mt-20">Организация: {$org_name}</div>
                    <div class="mt-20">Комментарий: {$comment}</div>
                </div>
            </div>
			<div>
				{$ttn_print_button}
			</div>
HTML;
	}

	public function view_body(){
		$this->get_data();
		$list_items = $this->lddt->detail_list_items($this->data);
		$result = [];
		$i = 0;
		foreach($list_items as $item){
			$i++;
            $p_data = \Price::get_all([
                "price" => $item["md_price"],
                "discount" => empty($r["md_discount"]) ? 0 : $item["md_discount"],
                "count" => $item["md_count"],
            ]);
            $total = $p_data["total"];
            $total_view = $this->lddt->lvp($total);
			$result[] = <<<HTML
				<tr>
					<td>{$i}</td>
					<td>{$item['md_name']}</td>
					<td>{$item['md_sku']}</td>
					<td class="text-center">{$item['md_count']}</td>
					<td>{$total_view}</td>
					<td class=" w20 text-right">
						<div class="checkbox-custom mb15">
							<input id="checkbox{$item['id']}" type="checkbox" name="checks[]" value="{$item['id']}" class="checks">
							<label for="checkbox{$item['id']}"></label>
						</div>
					</td>
				</tr>
HTML;
		}
		return implode("", $result);
	}
}