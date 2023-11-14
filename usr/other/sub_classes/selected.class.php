<?php

use Boot\Support\Currency;

abstract class Selected{
    protected $request = [];
    protected $query_list;
    protected $query_categories;
    protected $count_load = 30;
    protected $finds;
    protected $add_btn_id = "addBtn";
    protected $uid;

    public function __construct($request = []){
        global $core;
        $this->request = $request;
        $this->uid = $core->auth->user_info['id'];
    }

    public static function query_to_result($q){
        global $db;
        $query = $db->query($q);
        $result = [];
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $result[] = $r;
            }
        }
        return $result;
    } 

    public function get_query_list(){
        global $db;
        //[]
        $request = $this->request;
        $orderField = empty($request["orderField"]) ? 'c.id' : $request["orderField"];
        $orderType = empty($request["orderType"]) ? 'ASC' : $request["orderType"];
        $price_type = empty($request["price_type"]) ? 3 : $request["price_type"];
        $count_load = empty($request["count_load"]) ? $this->count_load : $request["count_load"];
        $page = empty($request["page"]) ? 1 : $request["page"];
        $page--;
        $search = empty($request["search"]) ? '' : $request["search"];
	    $search = trim($search);
	    $search = htmlspecialchars($search, ENT_QUOTES);
	    $checkWhere = [];
	    if(!empty($search)){
	    	$searchMas = explode(" ", $search);
	    	foreach($searchMas as $val){
	    		$checkWhere[] = "(c.title LIKE '%".$val."%' OR c.sku LIKE '%".$search."%' OR c.sku2 LIKE '%".$search."%' OR c.sku3 LIKE '%".$search."%' OR cm.name LIKE '%".$search."%')";
	    	}
	    }
	    if(count($checkWhere) > 0){
	    	$checkWhere = "(".implode(" AND ", $checkWhere).")";
	    } else {
	    	$checkWhere = "TRUE";
	    }
	    $checkWhere = "AND ".$checkWhere;

	    $searchCatWhere = "";
        if(!empty($request["category"])){
	    	$categories = get_all_categories_catalog();
	    	$categories_list = get_children_category($request["category"], $categories);
	    	$searchCatWhereArr = [];
	    	foreach($categories_list as $cl){
	    		$searchCatWhereArr[] = "c.category = ',".$cl.",'";
	    	}
	    	$searchCatWhere = " AND (".implode(" OR ", $searchCatWhereArr).")";
	    } 

	    $filterDataWhere = "";
	    if(isset($request["attribute"]) && count($request["attribute"]) > 0){
	    	foreach($request["attribute"] as $key => $val){
	    		if(count($val) > 0){
	    			$filterDataWhere .= " AND (";
	    			foreach($val as $v){
	    				$str = '"'.$key.'":"'.$v.'"';
	    				$str1 = '"'.$key.'":'.$v.'';
	    				$filterDataWhere .= "c.attribute LIKE '%".$str."%' OR c.attribute LIKE '%".$str1."%' OR ";
	    			}
	    			$filterDataWhere = mb_substr($filterDataWhere, 0, -4);
	    			$filterDataWhere .= ")";
	    		}
	    	}
	    }

	    $filterDataManWhere = "";
	    if(isset($request["man"]) && count($request["man"]) > 0){
	    	$filterDataMan = implode(", ", $request["man"]);
	    	$filterDataManWhere .= " AND (cm.id IN(".$filterDataMan."))";
        }
        
        $oid = find_org_uid($this->uid);
        

        $query_list = "SELECT c.id, c.title, c.sku, c.quantity, c.attribute, c.unit, cu.name as cu_name, c.man, cm.name as cm_name, c.preview, (sd.sum - IFNULL(sr.count, 0)) as count, 
        ROUND(IF(cg.discount IS NULL, cpd.sum, (cpd.sum * (1 - cg.discount / 100)) ), 2) as price
        FROM `" . DB_PREFIX . "_catalog` as c 
        LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as cm ON(cm.id = c.man) 
        LEFT JOIN `".DB_PREFIX."_catalog_unit` as cu ON(cu.unit = c.unit)
        LEFT JOIN (
            SELECT cid, sum FROM `".DB_PREFIX."_stock_detail` WHERE stock = 1
        ) as sd ON(sd.cid = c.id)
        LEFT JOIN (
            SELECT cpd.cid, 
            CASE 
                WHEN cp.price_type = 2 THEN cpd.sum * ".Currency::get_exchange()->get(978)."
                ELSE cpd.sum
            END AS sum
            FROM `".DB_PREFIX."_catalog_price_detail` as cpd 
            LEFT JOIN `".DB_PREFIX."_catalog_price` as cp ON(cp.id = cpd.price_id)
            WHERE cp.active = '1' AND cp.price_type IN (2, 3) HAVING 1 ORDER BY cp.date
        ) as cpd ON(cpd.cid = c.id)
        LEFT JOIN (
            SELECT cid, SUM(count) as count FROM `".DB_PREFIX."_stock_reserv` WHERE status = '1' AND stock_id = '1' GROUP BY cid
        ) as sr ON(sr.cid = c.id)
        LEFT JOIN (
            SELECT cgd.catalog_groups_id, cgd.discount
            FROM `".DB_PREFIX."_catalog_groups_discount` as cgd
            LEFT JOIN `".DB_PREFIX."_catalog_groups_organizations` as cgo ON(cgd.id = cgo.catalog_groups_discount_id)
            WHERE cgo.oid = '".$oid."'
        ) as cg ON(c.catalog_groups_id = cg.catalog_groups_id)
        WHERE c.hidden = 0 AND c.active = 1 ".$checkWhere.$searchCatWhere.$filterDataWhere.$filterDataManWhere."
        GROUP BY c.id
        ORDER BY ".$orderField." ".$orderType." 
        LIMIT ".$count_load * $page.", ".$count_load."";
        
        return $query_list;
    }

    public function view_finds_body(){
        $query = $this->get_query_list();
        $items = $this->query_to_result($query);
        $result = [];
        foreach($items as $item){
            $this->finds = $item;
            $result[] = $this->view_finds_body_item();
        }
        return implode("", $result);
    }

    public function get_categories(){
        global $db;
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_categories` WHERE hidden = 0 AND module = 'catalog_v2' ORDER BY name");
        $cats = [];
        while($cat = $db->getRow($query)){
            $cats[$cat['parent_id']][$cat['id']] = $cat;
        }
        $result = $this->tree_build_categories($cats, 0, true);
        return $result;
    }

    private function tree_build_categories($cats,$parent_id, $check = false){
        if(is_array($cats) and isset($cats[$parent_id])){
            if($check) {
                $tree = '<ul class="submain"><li><p id="point_" class="point check" data-id="">Без категории</p>';
            } else {
                $tree = '<ul class="submenu" id="submenu_'.$parent_id.'">'; 
            }
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li><p id="point_'.$cat["id"].'" class="point" data-id="'.$cat["id"].'">'.$cat['name'].'</p>';
                $tree .=  $this->tree_build_categories($cats,$cat['id']);
                $tree .= '</li>';
            }
            $tree .= '</ul>';
        } else return null;
        return $tree;
    }

    public function view_sorting($name, $field){
        return <<<HTML
        <div class="d-flex align-items-center sort_item" onclick="selected_sort(this, '{$field}');">
            <div class="sorting_name mr-1">{$name}</div>
            <div class="sorting_up" style="display:none;">
                <i class="fad fa-sort-up"></i>
            </div>
            <div class="sorting_down" style="display:none;">
                <i class="fad fa-sort-down"></i>
            </div>
        </div>
HTML;
    }

    public function view(){
        $class_name = get_class($this);
        $category = isset($this->request["category"]) ? $this->request["category"] : "";
        return <<<HTML
        <title>Tamara</title>
{$this->links()}
    <form id="mainForm" onsubmit="return false;">
    	<input type="hidden" name="page" value="1" id="page">
        <input type="hidden" name="category" value="{$category}" id="category">
        <input type="hidden" name="class_name" value="{$class_name}">
        <input type="hidden" name="orderField" value="" id="orderField">
        <input type="hidden" name="orderType" value="" id="orderType">
        <div id="grid_container_load">
    		<div class="grid_container__container">
    			<div class="floatingCirclesG">
    				<div class="f_circleG frotateG_01"></div>
    				<div class="f_circleG frotateG_02"></div>
    				<div class="f_circleG frotateG_03"></div>
    				<div class="f_circleG frotateG_04"></div>
    				<div class="f_circleG frotateG_05"></div>
    				<div class="f_circleG frotateG_06"></div>
    				<div class="f_circleG frotateG_07"></div>
    				<div class="f_circleG frotateG_08"></div>
    			</div>
    		</div>
    	</div>
        {$this->view_head()}
    	<div class="single-profile p-0 mt-2">	
    		<div class="mt-2 grid_container">
    			<div class="grid_container__search">
    				<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            {$this->input_group_prepend()}
    					</div>
    					<input type="text" class="form-control" placeholder="Введите артикул или наименование товара..." name="search" id="search">
    					<div class="input-group-append">
    					  <button class="btn btn-outline-secondary" type="button" onclick="ll({act: 'html'});">Поиск</button>
    					</div>
    				  </div>
    			</div>
    			<div class="grid_container__find">
                    <div id="grid_container__find-preview">
                        <div class="category_preview">
                            <div class="preview_text">Выберите категорию</div>
                            <div class="preview_icon d-flex justify-content-end"><i class="fal fa-arrow-alt-right"></i></div>
                        </div>
                        <div class="goods_preview d-flex align-items-center">
                            <div class="preview_icon"><i class="fal fa-arrow-alt-down"></i></div>    
                            <div class="preview_text ml-3">Выбранные товары</div>    
                        </div>
                        <div class="info_preview">
                            <i class="far fa-info-circle"></i>
                        </div>
                    </div>
    				<div id="grid_container__find_load_all">
    					<div class="grid_container__container">
    						<div class="floatingCirclesG">
    							<div class="f_circleG frotateG_01"></div>
    							<div class="f_circleG frotateG_02"></div>
    							<div class="f_circleG frotateG_03"></div>
    							<div class="f_circleG frotateG_04"></div>
    							<div class="f_circleG frotateG_05"></div>
    							<div class="f_circleG frotateG_06"></div>
    							<div class="f_circleG frotateG_07"></div>
    							<div class="f_circleG frotateG_08"></div>
    						</div>
    					</div>
    				</div>
    				<div class="find_table table-head">
                        {$this->view_finds_head()}
    				</div>
                    <div id="find_table_body" class="table-body all_text" onscroll="scroll_load(this);">
                        {$this->view_finds_body()}
                    </div>
    			</div>
    			<div class="grid_container__find_load">
    				<div id="fountainG">
    					<div id="fountainG_1" class="fountainG"></div>
    					<div id="fountainG_2" class="fountainG"></div>
    					<div id="fountainG_3" class="fountainG"></div>
    					<div id="fountainG_4" class="fountainG"></div>
    					<div id="fountainG_5" class="fountainG"></div>
    					<div id="fountainG_6" class="fountainG"></div>
    					<div id="fountainG_7" class="fountainG"></div>
    					<div id="fountainG_8" class="fountainG"></div>
    				</div>
    			</div>
    			<div class="grid_container__categories all_text">{$this->get_categories()}</div>
    			<div class="grid_container__filter all_text" id="filter"></div>
    			<div class="grid_container__goods">
    				<div class="goods_table table-head">
    					<div class="goods_table__title">Наименование</div>
    					<div class="goods_table__sku">Артикул</div>
    					<div class="goods_table__man">Производитель</div>
    					<div class="goods_table__count">Количество</div>
    					<div class="goods_table__price">Цена</div>
    					<div class="goods_table__total">Сумма</div>
    					<div class="goods_table__actions">Действия</div>
    				</div>
    				<div class="table-body all_text" id="goods_table">{$this->view_goods_body()}</div>
    			</div>
    		</div>
    	</div>
    </form>
{$this->scripts()}
HTML;
    }

    public function scripts(){
        return <<<HTML
            <script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/jquery-3.4.1/jquery-3.4.1.min.js?v=5e4129400044f"></script>
            <script
              src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>
            <script>
                $.widget.bridge('uibutton', $.ui.button);
                $.widget.bridge('uitooltip', $.ui.tooltip);
            </script>
            <script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/popper/popper.min.js?v=5ea6af0bb1530"></script>
            <script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/bootstrap-4.3.1/dist/js/bootstrap.min.js?v=5e422375b7758"></script>
            <script src="/usr/tpl/dev2/assest/js/priceList.js" charset="utf-8"></script>
            <script src="/usr/tpl/dev2/assest/js/jquery.arcticmodal-0.3.min.js" type="text/javascript" charset="utf-8"></script>
            <script src="/usr/tpl/dev2/assest/js/catalogView.js" charset="utf-8"></script>
            <script src="/usr/other/sub/price/js/price.js"></script>
            <script src="/usr/tpl/dev2/assest/js/selected/selected.js"></script>
HTML;
    }

    public function links(){
        return <<<HTML
            <link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/vendor/bootstrap-4.3.1/dist/css/bootstrap.min.css?v=5ebe756b517b6">
            <link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/simple.css">
            <link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/jquery.arcticmodal-0.3.css">
            <link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/fonts/fontawesome/css/all.min.css?v=5ebe756b50474">
            <link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/style.css">
            <link rel="stylesheet" href="/usr/tpl/dev2/assest/css/selected/selected.css">
HTML;
    }

    public function view_head(){

    }

    public function view_goods_body(){
        
    }

    public function input_group_prepend(){

    }

    public static function get_btn($request){
        //["url" => ""]
        //"name" => "",
        $name = empty($request["name"]) ? "Добавить товар" : $request["name"];
        $url = empty($request["url"]) ? "" : $request["url"];
        $class = empty($request["class"]) ? "" : $request["class"];
        return <<<HTML
        <a href="{$url}" onclick="javascript:void window.open('{$url}','1589526963985','width=1225,height=800,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;" class="{$class}">{$name}</a>
HTML;
    }

    abstract public function view_finds_head();
    abstract public function view_finds_body_item();

}