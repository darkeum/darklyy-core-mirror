<?php

class SelectedSiteSpec extends Selected{
    public function view_finds_head(){
        return <<<HTML

            <div class="find_table__title">{$this->view_sorting('Наименование', 'c.title')}</div>
            <div class="find_table__sku">{$this->view_sorting('Артикул', 'c.sku')}</div>
            <div class="find_table__man">{$this->view_sorting('Производитель', 'cm_name')}</div>
            <div class="find_table__price">{$this->view_sorting('Цена', 'price')}</div>
            <div class="find_table__actions">Действия</div>

HTML;
    }

    public function view_finds_body_item(){
        // $priceData = getCatalogPrice(["cid" => $this->finds["id"], "type" => 3]);
        // $price = empty($priceData["data"]["price"]) ? 'По запросу' : $priceData["data"]["price"];
        $price = empty($this->finds["price"]) ? 'По запросу' : $this->finds["price"];
        return <<<HTML
            <div class="find_table find_table-item" id="item_{$this->finds['id']}">
			    <div class="find_table__title">{$this->finds["title"]}</div>
			    <div class="find_table__sku">{$this->finds["sku"]}</div>
			    <div class="find_table__man">{$this->finds["cm_name"]}</div>
			    <div class="find_table__price">{$price}</div>
			    <div class="find_table__actions">
				    <div class="btn-group" role="group" aria-label="Basic example">
					    <i class="fas fa-plus btn btn-sm bs_tooltip" data-toggle="tooltip" data-placement="top" title="Добавить" onclick="addGoods({$this->finds['id']});"></i>
                        <i class="fas fa-search-plus btn btn-sm bs_tooltip" data-toggle="tooltip" data-placement="top" title="Быстрый просмотр" onclick="viewProsmotr(this);" 
                        data-src="/catalog_v2/fast_view/{$this->finds['id']}"></i>
				    </div>
			    </div>
		    </div>
HTML;
    }

    public function view_head(){
        global $core;
        $id = $this->add_btn_id;
        $core->tpl->loadFile('profile/specification/profile.edit_head');
        $core->tpl->setVar('ADD_BTN_ID',$id);
		$selected_head = $core->tpl->return_end();
		$core->tpl->loadFile('profile/specification/profile.edit_fields');
        $selected_fields = $core->tpl->return_end(); 
        
        return $selected_head.$selected_fields;
    }

    public function scripts(){
        return <<<HTML
            <script
              src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>
            <script>
                $.widget.bridge('uibutton', $.ui.button);
                $.widget.bridge('uitooltip', $.ui.tooltip);
            </script>
            <script src="/usr/other/sub/price/js/price.js"></script>
            <script src="/usr/tpl/dev2/assest/js/selected/selected.js"></script>
            <script src="/usr/tpl/dev2/assest/js/specification/spec_edit.js"></script>
HTML;
    }

    public function links(){
        return <<<HTML
            <link rel="stylesheet" href="/usr/tpl/dev2/assest/css/selected/selected.css">
HTML;
    }

    public function view_goods_body(){
        $spec_list = get_spec_list($this->request["id"]);
        $goods = [];
        foreach($spec_list as $val){
            $goods[] = get_goods_tov_template($val);
        }
        $goods = implode("", $goods);
        return $goods;
    }
}