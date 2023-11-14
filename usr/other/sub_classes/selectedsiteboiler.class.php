<?php

class SelectedSiteBoiler extends Selected{
    public $save_func_name = 'save_tov();';


    public function view_finds_head(){
        return <<<HTML

            <div class="find_table__preview"><i class="fas fa-image"></i></div>
            <div class="find_table__title">{$this->view_sorting('Наименование', 'c.title')}</div>
            <div class="find_table__sku">{$this->view_sorting('Артикул', 'c.sku')}</div>
            <div class="find_table__man">{$this->view_sorting('Производитель', 'cm_name')}</div>
            <div class="find_table__price">{$this->view_sorting('Цена', 'price')}</div>
            <div class="find_table__count">{$this->view_sorting('Количество', 'count')}</div>
            <div class="find_table__actions">Действия</div>

HTML;
    }

    public function view_finds_body_item(){
        // $priceData = getCatalogPrice(["cid" => $this->finds["id"], "type" => 3]);
        // $price = empty($priceData["data"]["price"]) ? 'По запросу' : $priceData["data"]["price"];
        $price = empty($this->finds["price"]) ? 'По запросу' : $this->finds["price"];
        if($this->finds["count"] > 0){
            $count = 'В наличии';
            $count_class = "has_count";
        } else {
            $count = 'По запросу';
            $count_class = "";
        }

        $preview = empty($this->finds["preview"]) ? '/files/no_photo.jpg' : $this->finds['preview'];

        
return <<<HTML
            <div class="find_table find_table-item" id="item_{$this->finds['id']}">
			    <div class="find_table__preview"><img class="find_table__preview-img" src="{$preview}" alt="preview" srcset=""></div>
			    <div class="find_table__title">{$this->finds["title"]}</div>
			    <div class="find_table__sku">{$this->finds["sku"]}</div>
			    <div class="find_table__man">{$this->finds["cm_name"]}</div>
			    <div class="find_table__price">{$price}</div>
			    <div class="find_table__count {$count_class}">{$count}</div>
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
        return <<<HTML
		<input type="hidden" value="{$this->request['id']}" name="id">
HTML;
    }

    public function scripts(){
        $scripts = parent::scripts();
        return <<<HTML
        {$scripts}
        <script src="/usr/tpl/dev2/assest/js/boiler/selected_boiler.js"></script>
HTML;
    }

    public function links(){
        $links = parent::links();
        return <<<HTML
        {$links}
        <link rel="stylesheet" href="/usr/tpl/dev2/assest/css/selected/selected_boiler.css">
HTML;
    }

    public function input_group_prepend(){
        $id = $this->add_btn_id;
        return <<<HTML
        <button class="btn btn-secondary" onclick="{$this->save_func_name}" id="{$id}" disabled>Добавить</button>
HTML;
    }

    
}