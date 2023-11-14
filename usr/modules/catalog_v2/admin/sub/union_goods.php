<?php

global $adminTpl, $core, $db, $core, $config, $lang;

function union_goods()
{
    global $adminTpl, $core, $db, $core, $config, $lang;
    $adminTpl->admin_head('Каталог товаров | Объединение товаров');
    ?>
<link rel="stylesheet" href="/usr/modules/catalog_v2/admin/asset/css/union_goods.css">
<div id="content" class="animated fadeIn">
    <div class="panel panel-dark panel-border top">
        <div class="panel-heading">
            <span class="panel-title">Объединение товаров</span>
        </div>
        <div class="panel-body pn">
            <div class="col-md-6">
                <h3>Поиск</h3>
                <input class="form-control" type="text" placeholder="Введите id или наименованное или артикул" id="search">
                <div id="ajaxContent"></div>
            </div>
            <div class="col-md-6">
                <button class="btn btn-success btn-sm btn-block" disabled id="unionBtn">Объединить</button>
                <table id="unionList" class="table">
                    <thead>
                        <th class="col-sm-10">Наименование</th>
                        <th class="col-sm-2">Действие</th>
                    </thead>
                    <tbody id="unionListBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/union_goods.js"></script>';
    $adminTpl->admin_foot();
}

function union_goods_search($value){
    global $adminTpl, $core, $db, $core, $config, $lang;
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog` WHERE (id = '".$value."' OR title LIKE '%".$value."%' OR sku LIKE '".$value."%') AND hidden = '0' LIMIT 100");
    $array = ["data" => []];
    $i = 0;
    while($r = $db->getRow($query)){
        $array["data"][$i]["id"] = $r["id"];
        $array["data"][$i]["quantity"] = get_quantity_stock_transaction($r["id"], 1);
        $array["data"][$i]["title"] = htmlspecialchars_decode($r["title"], ENT_QUOTES);
        $i++;
    }
    $result = json_encode($array, JSON_UNESCAPED_UNICODE);
    echo $result;
}

function union_goods_final($data){
    global $adminTpl, $core, $db, $core, $config, $lang;
    $favorite = $data["data"]["favorite"];
    $idStr = $favorite.", ";
    $idStr .= implode(", ", $data["data"]["id"]);
    $query = $db->query("SELECT id FROM `".DB_PREFIX."_catalog` WHERE id IN(".$idStr.")");
    $quantity = 0;
    $favorite_count = 0;

    $source = [];
	$source['module'] = 'catalog_v2';
	$source['pid'] = 0;
    $source['comment'] = 'Объединение остатков с товаром №'.$favorite;

    $save_id = [];
    while($r = $db->getRow($query)){
        $old_quantity = get_quantity_stock_transaction($r["id"], 1);
        $quantity += $old_quantity;

        if((int)$r["id"] !== (int)$favorite){
            $db->query("UPDATE `".DB_PREFIX."_catalog` SET hidden = '1' WHERE id = '".$r["id"]."' LIMIT 1");
            updateQuantity($r["id"], '1', 0, 'set', $source);
            setLog("info", $r["id"], "catalog_v2", 'Объединение номенклатуры id(количество было): '.$r["id"].'('.$old_quantity.') -> '.$favorite);
        }

        if((int)$r["id"] === (int)$favorite){
            $favorite_count = $old_quantity;
        }
    }

    $source['comment'] = "Объединение остатков товара №{$favorite} с товарами: ".implode(", ", $data["data"]["id"]);
    updateQuantity($favorite, '1', $quantity, 'set', $source);
    setLog("info", $favorite, "catalog_v2", 'Финальный товар объединения номенклатуры id(количество было): '.$favorite.'('.$favorite_count.') -> '.$favorite.'('.$quantity.')');
}