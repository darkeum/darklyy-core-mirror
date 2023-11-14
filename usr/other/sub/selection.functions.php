<?php

//***HELP***
//$url = "/administration/module/sales/addProduct/"
function callAddProduct($id, $url, $return = false)
{
    $str = '<a href="' . $url . $id . '" class="btn btn-xs btn-success AddProduct" onclick="'.clickCallAddProduct($id, $url).'">Добавить товар</a>';
    if($return){
        return $str;
    } else {
        echo $str;
    }
}

function clickCallAddProduct($id, $url){
    return 'javascript:void window.open(\'' . $url . $id . '\',\'1549627856974\',\'width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0\');return false;';
}

//***HELP***
//$send = "/administration/module/sales/ajax/order_tovar_add/";
//$update = "/administration/module/sales/ajax/order_tovar_list/";

function addProduct($id, $send, $update, $update_name = "#tov_list", $type = '1', $module = null)
{
	
	//type = 2 - для изменения
    //type = 3 - для быстрой корзины 
    //type = 4 - report 1-о добавление
    global $adminTpl, $config, $core, $admin_conf, $db;

    require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");

    ?>
    <!doctype html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Alice</title>
        <link rel="shortcut icon" href="/usr/other/sub/selection/img/favicon.png" type="image/png">
        <link rel="stylesheet" href="/usr/other/sub/selection/css/bootstrap.min.css">
        <link rel="stylesheet" href="/usr/other/sub/selection/css/select2.min.css">
        <link rel="stylesheet" href="/usr/other/sub/selection/css/jquery-ui.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <link rel="stylesheet" href="/usr/other/sub/selection/css/selection.css">
        <?php
            ndr_style();
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h3>Подбор товаров в Заказ клиента</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: 10px;">
                    <div class="col-md-2">
                        <input type="button" id="submit" class="btn btn-success" value="Перенести в документ" <?php echo (($type != "1") && ($type != "4") ? "disabled" : ""); ?>>
                    </div>
                    <div class="col-md-2">
                    <input type="button" id="openCreateGoods" class="btn btn-primary" value="Создать номенклатуру">
                    </div>
                    <div class="col-md-2">
                        <select class="js-example-basic-single" id="price_type">
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="js-example-basic-single" id="stock_id" title="Склад" onchange="defLoad();">
                            <?php
                                echo get_stock_select_option();
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6" style="padding-right: 0">
                    <input type="text" class="form-control" name="search" id="search" placeholder="Введите наименование товара или номер артикула...">
                </div>
                <div class="col-xs-1" style="padding: 0">
                    <input type="button" class="btn" value="X" id="clear">
                </div>
                <div class="col-xs-5">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" for="exactCoincidence">
                            <input class="form-check-input" type="checkbox" id="exactCoincidence" value="1" onchange="defLoad();"> По точному совпадению
                        </label>
                        <label class="form-check-label" for="catalogSearch">
                            <input class="form-check-input" type="checkbox" id="catalogSearch" value="1" onchange="defLoad();"> По каталогу
                        </label>
                        <label class="form-check-label" for="check_full">
                            <input class="form-check-input" type="checkbox" id="check_full" value="1" onchange="defLoad();"> Полнотекстовый поиск
                        </label>
                        <label class="form-check-label" for="check_expert">
                            <input class="form-check-input" type="checkbox" id="check_expert" value="1" onchange="defLoad();"> Режим эксперта
                        </label>
                        <label class="form-check-label" for="check_active">
                            <input class="form-check-input" type="checkbox" id="check_active" value="1"  onchange="defLoad();" checked> Активная номенклатура
                        </label>
                        <button id="filterClear" class="btn btn-sm btn-info">Сбросить фильтр</button>
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <div class="cssload-loader">
                        <div class="cssload-inner cssload-one"></div>
                        <div class="cssload-inner cssload-two"></div>
                        <div class="cssload-inner cssload-three"></div>
                    </div>
                    <div class="tableMagic" id="ajaxResDiv">
                        <table id="resTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th data-name="c.title">
                                        <div class="sortth">
                                            <div>Наименование</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios1_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios1_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios1_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" id="exampleRadios1_2" value="1" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th data-name="c.sku">
                                        <div class="sortth">
                                            <div>Артикул</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios2_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios2_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios2_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" value="1" id="exampleRadios2_2" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th data-name="cm.name">
                                        <div class="sortth">
                                            <div>Производитель</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios3_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios3_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios3_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" value="1" id="exampleRadios3_2" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th data-name="sum">
                                        <div class="sortth">
                                            <div>Цена(руб.)</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios5_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios5_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios5_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" id="exampleRadios5_2" value="1" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th title="Наличие / Доступно / Резерв" data-name="quantity">
                                        <div class="sortth">
                                            <div>Н/Д/Р</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios4_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios4_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios4_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" id="exampleRadios4_2" value="1" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th data-name="cu.name">
                                        <div class="sortth">
                                            <div>Ед. изм.</div>
                                            <div class="sortRadio">
                                                <label for="exampleRadios4_1"><i class="fas fa-sort-up sortIcon"></i></label> 
                                                <label for="exampleRadios4_2"><i class="fas fa-caret-down sortIcon"></i></label>
                                                <input type="radio" id="exampleRadios4_1" value="1" name="sort" data-sort="DESC" class="changeSort">
                                                <input type="radio" id="exampleRadios4_2" value="1" name="sort" data-sort="ASC" class="changeSort">
                                            </div>
                                        </div>
                                    </th>
                                    <th style="width: 150px;">Действие</th>
                                </tr>
                            </thead>
                            <tbody id="ajaxRes">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-3">
                    <div id="ajaxCat" onmousedown="return false" onselectstart="return false">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
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
            </div>
            <hr id="size_line">
            <form id="myForm" method="POST" onsubmit="return false;">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-9">
                        <div class="tableMagic" id="basketDiv">
                            <table id="basTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Артикул</th>
                                        <th>Производитель</th>
                                        <th>Цена(руб.)</th>
										<th>Скидка</th>
                                        <th title="Наличие / Доступно / Резерв">Н/Д/Р</th>
                                        <th>Ед. изм.</th>
                                        <th>Кол-во</th>										
										<th>Сумма</th>
                                        <th>Действие</th>
                                    </tr>
                                </thead>
                                <tbody id="basket">

                                </tbody>
                            </table>
                        </div>
						<div style="text-align: right; padding-right: 20px;"><b>Итого: </b><span id="total_all">0 руб.</span></div>
                    </div>
                    <div class="col-md-3">
                        <div id="filter" onmousedown="return false" onselectstart="return false">

                        </div>
                    </div>
                </div>
                <input type="hidden" value="0" id="loaded_max">
                <input type="hidden" value="<?php echo $id; ?>" name="id" id="mainId">
                <input type="hidden" value="c.id" id="orderField">
                <input type="hidden" value="ASC" id="orderType">
                <input type="hidden" value="<?php echo $type; ?>" name="typeSelection" id="typeSelection">
                <input type="hidden" value="<?php echo $module; ?>" name="module" id="module">
            </form>
        </div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                    </div>
                    <div class="modal-body" id="dataLoad">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addGoods" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="" method="POST" id="createGoods">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Добавление номенклатуры</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Наименование:</label>
                                <input type="text" class="form-control" id="title" placeholder="Введите наименование" required>
                            </div>
                            <div class="form-group">
                                <label for="cat">Категория:</label>
                                <select class="js-example-basic-single" id="cat" required>
                                </select>
                            </div>
                            <label for="sku">Артикул:</label>
                            <div class="input-group col-sm-12">
                                <input type="text" class="form-control" id="sku" placeholder="Введите артикул" required>
                                <span class="input-group-btn">
                                    <button type="button" id="skuGenerate" class="btn"><i class="fas fa-sync-alt"></i></button>
                                </span>
                            </div>
                            <div id="infoSku" style="display:none;"></div>

                            <div class="form-group">
                                <label for="unit">Производитель:</label>
                                <select class="js-example-basic-single" id="man">
                                    <option value="-1" selected disabled>Выберите производителя</option>
                                    <?php
                                        $query_manufacture_list = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_manufacture ORDER BY name ASC");
                                        if($db->numRows($query_manufacture_list) > 0) 
                                        {
                                            while($manufacture_list = $db->getRow($query_manufacture_list)) 
                                            {
                                                echo '<option value="'.$manufacture_list['id'].'">'.$manufacture_list['name'].'</option>';
                                            }
                                        }	
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit">Единицы измерения:</label>
                                <select class="js-example-basic-single" id="unit" required>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="types">Тип номенклатуры:</label>
                                <select class="js-example-basic-single" id="types" required>
                                    <option value="3" selected>Товары</option>
                                    <option value="4">Услуги</option>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Создать</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
            ndr_modal();
        ?>

        <script src="/usr/other/sub/selection/js/jquery.min.js"></script>
        <script src="/usr/other/sub/selection/js/bootstrap.min.js"></script>
        <script src="/usr/other/sub/selection/js/js.cookie.min.js"></script>
        <script src="/usr/other/sub/selection/js/select2.min.js"></script>
        <script src="/usr/other/sub/selection/js/jquery-ui.js"></script>
        <script src="/usr/other/sub/selection/js/colResizable-1.6.min.js"></script>
        <script src="/usr/other/sub/selection/js/script.js?<?php echo time();?>"></script>

        <?php
            echo ndr_script_re();
        ?>

        <script>
            $(function() {
                //отправка данных из корзины
                $("#submit").click(function() {
                    let type = +$("#typeSelection").val();
                    let form_data = $("#myForm").serialize();
                    let mainId = $("#mainId").val();
                    const module = "<?php echo $module;?>";
                    switch(type){
                        default:
                            $(this).attr("disabled", true);
                            $.post("<?php echo $send . $id; ?>", form_data, function() {
                                switch(module){
                                    default:
                                        $.post("<?php echo $update . $id; ?>", function(data) {
                                            $("<?php echo $update_name; ?>", opener.window.document).html(data);
                                            window.close();
                                        });
                                    break;
                                    case "merging":
                                        opener.updateHeader();
                                        opener.updateBody();
                                        window.close();
                                    break;
                                }
                                
                            });
                        break;
                        case 4:
                            if(mainId === ""){
                                let setting = opener.getLocalSetting();
                                let type = $("#type", window.opener.document).val();
                                let array = [];
                                if(Array.isArray(setting[type][2]["value"])){
                                    array = setting[type]["2"]["value"];
                                }
                                $(".bas2").each(function(i, elem){
                                    let id = $(elem).val();
                                    let text = $("#bas_"+id).children().eq(0).text();
                                    array.push({id:id, text:text});
                                });
                                opener.changeSetting("change", [2, "value"], array);
                                opener.drawSetting();
                            } else {
                                let setting = opener.getSetting();
                                let array = setting["data"][mainId]["values"];
                                $(".bas2").each(function(i, elem){
                                    let id = $(elem).val();
                                    let text = $("#bas_"+id).children().eq(0).text();
                                    array.push({id:id, text:text});
                                });
                                opener.changeProp(mainId, "data", "values", array, true);
                            }
                            
                            window.close();
                        break;
                    }
                    
                });

            });
        </script>

    </body>

    </html>
<?php
}
