<?php

namespace Merging;

use Boot\System\Office\OfficeSupport;

class MergingAdditionalTemp{
    static $lnn = 'Доукомплектация товаров';
    static function index(){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang;
        $adminTpl->admin_head(static::$lnn);
        $atributs = array();
        $atributs['opts'] = 'open';
        $atributs['module'] = 'module';
        $atributs['module_name'] = 'merging';
        $atributs['module_open'] = 'detail_additional';
        loadGrid('jqMergingAdditional', static::$lnn, $atributs, ROOT.'/usr/modules/merging/admin/grid/');
        $adminTpl->admin_foot();
    }

    static function show($id, $request){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang;
        $display = $request["display"] ?? "index";
        $ma = \Merging\Model\MergingAdditional::findOrFail($id);
        if($display == "index" || $display == "header"){
            if($ma->active == 0){
                $btn = <<<HTML
                    <div class="ph10">
		            	<a href="javascript:void(0);" 
                        onclick="end_quest(1)"
                        class="holder-style p15 text-center holder-active">
                            <span class="far fa-check-circle holder-icon"></span>
                            <br>Завершить<br>заявку
                        </a>
		            </div>
HTML;
            } else {
                $btn = <<<HTML
                    <div class="ph10">
		            	<a href="javascript:void(0);"  
                        onclick="end_quest(0);" 
                        class="holder-style p15 text-center holder-active">
                            <span class="fas fa-ban holder-icon"></span>
                            <br>Отменить<br>завершение
                        </a>
		            </div>
                    <div class="ph10">
		            	<a href="/administration/module/merging/additional_print/{$id}" class="holder-style p15 text-center holder-active">
                            <span class="glyphicon glyphicon-print holder-icon"></span>
                            <br>Печать<br>&emsp;
                        </a>
		            </div>
HTML;
            }
            $ndr_view = get_ndr(["cid" => $ma->merging->cid, "stock_id" => $ma->merging->stock_id, "returnType" => "view"]);
            $stock_name = get_stock_name($ma->merging->stock_id);
            $org = getOrgInfo($ma->merging->org);
            $uid_name = shortUserName($ma->uid);
            $date = date("d.m.Y", strtotime($ma->date));
            $url_update = "/administration/module/merging/x_change_additional";
            $header = <<<HTML
                <div class="col-md-3">
                    <div class="element-groups-lg-custom wizard-options">{$btn}</div>
                </div>
                <div class="col-md-2">
                    <small title="Наличие / Доступно / Резерв">Н/Д/Р:</small><br/>
                    <b>{$ndr_view}</b><br/><br/>
                    <small>Количество:</small><br/>
                    <b><span>{$ma->merging->count}</span></b><br/><br/>
                    <small>Товар:</small><br/>
                    <b>{$ma->merging->name}</b><br/><br/>
                </div>
                <div class="col-md-2">
                    <small>Тип:</small><br/>
                    <b>Доукомплектация</b><br/><br/>
                    <small>Склад:</small><br/>
                    <b>{$stock_name}</b><br/><br/>
                    <small>Организация:</small><br/>
                    <b>{$org["organizations"]["name"]}</b>
                </div>
                <div class="col-md-2">
                    <small>Ответственный:</small><br/>
                    <b>{$uid_name}</b><br/><br/>
                    <small>Дата:</small><br/>
                    <b><span id="date" data-pk="{$id}" data-url="{$url_update}">{$date}</span></b><br/><br/>
                    <small>Объект:</small><br/>
                    <b>{$ma->merging->object_->name}</b>
                </div>
                <div class="col-md-2">
                    <small>Комплектация:</small><br/>
                    <b><a href="/administration/module/merging/detail/{$ma->merging_id}" target="_blank">{$ma->merging->numb}</a></b>
                </div>
HTML;
            if($display == "header"){
                echo $header;
                return;
            }
        }

        if($display == "index" || $display == "body"){
            $body = [];
            $i = 0;
            $url_detail_update = "/administration/module/merging/x_change_detail_additional";
            foreach($ma->merging_detail_additional as $item){
                $i++;
                $ndr_view = get_ndr(["cid" => $item->cid, "stock_id" => $ma->merging->stock_id, "returnType" => "view"]);
                $total = $item->price * $item->count;
                $unit_name = get_unit('data', $item->unit ,'sname');
                $btn_disable = $ma->active == 0 ? "" : "disabled";
                $url_delete = "/administration/module/merging/destroy_additional/{$item->id}";
                $body[] = <<<HTML
                    <tr class="tr_item" id="tr_{$item->id}" data-id="{$item->id}" data-cid="{$item->cid}">
                        <td>{$i}</td>
                        <td>{$item->name}</td>
                        <td>{$item->sku}</td>
                        <td class="text-center">{$ndr_view}</td>
                        <td class="text-center"><span class="count" data-pk="{$item->id}" data-url="{$url_detail_update}">{$item->count}</span></td>
                        <td class="text-center">{$unit_name}</td>
                        <td><span class="price" data-pk="{$item->id}" data-url="{$url_detail_update}">{$item->price}</span></td>
                        <td class="total">{$total}</td>
                        <td class="text-center"><button class="btn btn-xs btn-danger" title="Удалить" onclick="remove_item_modal('{$url_delete}')" {$btn_disable}><i class="fas fa-times"></i></button></td>
                        <td class="text-right">
                            <div class="checkbox-custom mb15">
			            		<input id="checkbox{$item->id}" type="checkbox" name="checks[]" value="{$item->id}" class="checks">
			            		<label for="checkbox{$item->id}>"></label>
			            	</div>
                        </td>
                    </tr>
HTML;
            }
            $body = implode("", $body);
            if($display == "body"){
                echo $body;
                return;
            }
        }
        
        $victoria = new \Victoria;
        $victoria->link = "/administration/module/merging/victoria_add_additional/{$id}";
        $v_btn = $ma->active == 0 ? $victoria->button() : "";
        $buttons = <<<HTML
            {$v_btn}
HTML;
        if($display == "buttons"){
            echo $buttons;
            return;
        }
        $adminTpl->admin_head(static::$lnn);
        echo <<<HTML
            <input type="hidden" id="router_show" value="/administration/module/merging/detail_additional/{$id}">
            <input type="hidden" id="router_check_minus_count" value="/administration/module/merging/additional_check_minus_count/{$id}">
            <input type="hidden" id="router_additional_end" value="/administration/module/merging/additional_end/{$id}">
            <section id="content" class="">
                <h3 class="text-center">
                    Заявка №{$id}
                </h3>
                <div class="row" id="admin-form">
                    <div class="panel mb25">
                        <div class="panel-body p20 pb10">
                            <div class="section row mbn admin-form">
                                <div class="col-md-12 pl15">
                                    <div class="section row mb15" id="header_container">
                                        {$header}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="row">
	        		<div class="panel panel-dark panel-border top">
	        			<div class="panel-heading">
	        				<span class="panel-title">Список товаров:</span>
	        				<div class="widget-menu pull-right" id="detail_body_btn">
                                {$buttons}					
	        				</div>	
                      </div>
            
                      <div class="panel-body pn table-responsive"> 
	        			<form id="tablesForm" method="POST" onsubmit="return false;">
                          <table class="table table-striped">
                            <thead id="table_thead">
                                <tr>
	        	                	<th class="w40"><span class="pd-l-sm"></span>№</th>
	        	                	<th class="w200">Номенклатура</th>
	        	                	<th class="w150 sku-units" data-type="1"><span>Артикул</span></th>
                                    <th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>	
	        	                	<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
	        	                	<th class="w100 text-center" data-toggle="tooltip" title="Ед. изм.">Ед. изм.</th>
	        	                	<th class="w100" data-toggle="tooltip" title="Цена">Цена</th>			
	        	                	<th class="w100" data-toggle="tooltip" title="Сумма">Сумма</th>			
	        	                	<th class="w100 text-center">Действия</th>
	        	                	<th class="w20 text-right">
	        	                		<div class="checkbox-custom mb15">
	        	                			<input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); return true;" class="checks">
	        	                			<label for="all"></label>
	        	                		</div>	
	        	                	</th>
	        	                </tr>
                            </thead>
                            <tbody id="tov_list">	
                                {$body}
	        				</tbody>
	        			<tfoot class="footer-menu">
                            <tr align="right">                    
	        				  <td align="right" colspan="15">
                                <nav align="right" class="text-right">						
	        						<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">							
            
	        						</select>
	        						<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" disabled><span         class="pd-l-sm"></span>	
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
HTML;
        xeditableInit();
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/modules/merging/admin/asset/js/merging_detail_additional.js?'.time().'"></script>';
        $adminTpl->admin_foot();
    }

    static function check_minus_count($id){
        $ma = \Merging\Model\MergingAdditional::findOrFail($id);
        if($ma->active == 1){
            return ["status" => true, "data" => []];
        } else {
            $data = [];
            foreach($ma->merging_detail_additional as $item){
                $data[$item->id] = [
                    "pid" => $item->id,
                    "module" => "merging_additional",
                    "count" => $item->count,
                    "cid" => $item->cid,
                    "stock_id" => $ma->merging->stock_id,
                ];
            }
            $check_id = check_minus_count_custom(["data" => $data, "module" => "merging_additional", "pid" => $id]);
            return [
                "status" => empty($check_id),
                "data" => empty($check_id) ? [] : $ma->merging_detail_additional->where("id", $check_id)->first(),
            ];
        }
    }

    static function end($id, $request){
        $active = $request["active"];
        $source = [];
	    $source['module'] = 'merging_additional';
	    $source['pid'] = $id;
	    $source['comment'] = $active == 1 ? "Проведение доукомплектации №{$id}" : "Отмена проведение доукомплектации №{$id}";
        $ma = \Merging\Model\MergingAdditional::findOrFail($id);
        $ma->active = $active;
        $ma->save();
        $action = $active == 1 ? "minus" : "plus";
        foreach($ma->merging_detail_additional as $item){
            updateQuantity($item->cid, $ma->merging->stock_id, $item->count, $action, $source);
        }
        return $ma;
    }

    static function print($id){
        $ma = \Merging\Model\MergingAdditional::findOrFail($id);

        $document = new \PhpOffice\PhpWord\TemplateProcessor(ROOT."/usr/modules/merging/resources/templates/merging_additional.docx");
        $document->setValue('numb', $ma->id);
        $document->setValue('date', date("d.m.Y", strtotime($ma->date)));
        $document->setValue('object_name', $ma->merging->object_->name);
        $document->setValue('stock_name', $ma->merging->stock->name);
        $document->setValue('org_name', $ma->merging->org_->name);
        $order_data = [];
        $n = 0;
        foreach($ma->merging_detail_additional as $item){
            $n++;
            $order_data[] = [
                "n" => $n,
                "sku" => $item->sku,
                "name" => $item->name,
                "s" => $item->count,
                "u" => get_unit('data', $item->unit ,'sname'),
            ];
        }
        $document->cloneRowAndSetValues('n', $order_data);
        $file_url = ROOT."/usr/modules/logistics/resources/templates/Доукомплектация_{$ma->id}.docx";
        $document->saveAs($file_url);
        OfficeSupport::get_file($file_url);
    }
}