<?php

use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

function index_catalog_groups(){
    global $url, $adminTpl;
    switch(isset($url[4]) ? $url[4] : null){
        default:
          catalog_groups();
        break;
        case "add":
          $cg = \Catalog_v2\Model\CatalogGroups::create($_POST);
          echo $cg->id;
        break;
        case "delete":
            $id = $url[5];
            \Catalog_v2\Model\CatalogGroups::destroy($id);
        break;
        case "edit":
            $id = $url[5];
            $adminTpl->admin_head('Каталог товаров | ');
            echo catalog_groups_edit($id);
            $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups.js"></script>';
            $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups_discount.js"></script>';
            $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups_edit.js?'.time().'"></script>';
            $adminTpl->admin_foot();
        break;
        case "update":
          \Catalog_v2\Model\CatalogGroups::findOrFail($url[5])->update($_POST);
        break;
        case "catalog_groups_discount":
          $id = $url[5];
          echo catalog_groups_discount($id);
        break;
        case "download_excel":
          catalog_groups_download_excel();
        break;
        case "save_excel":
          catalog_groups_save_excel($_FILES);
        break;
        case "save_discount_excel":
          catalog_groups_discount_save_excel($_FILES);
        break;
        case "catalog_groups_table":
          $id = $url[5];
          $table_data = catalog_groups_table($id);
          echo $table_data["template"];
        break;
    }
}

function catalog_groups(){
  global $db, $adminTpl, $core;
  $lnn = 'Группы товаров';
  $adminTpl->admin_head('Менеджер каталога | '.$lnn);

  $atributs = array();
  $atributs['opts'] = 'open';
  $atributs['module'] = 'module';
  $atributs['module_name'] = 'catalog_v2/groups';
  $atributs['module_open'] = 'edit';	
  $atributs['button'] = 'Действия';
  $atributs['button_url'] = '/administration/module/catalog_v2/groups/add';
  $atributs['button_onclick'] = 'switch_modal();';
  load_grid('jqCatalogGroups', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');
  $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups.js"></script>';
  $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups_main.js?'.time().'"></script>';
	
  $category_opt = array_to_options($core->aCatList('catalog_v2'));

  ?>

      <div class="modal fade" id="switchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Выберите действие</h5>
              </div>
              <div class="modal-body">
                  <button class="btn btn-primary btn-sm btn-block" onclick="location.href='/administration/module/catalog_v2/groups/download_excel';" data-dismiss="modal">Загрузить группы товаров в Excel</button>
                  <button class="btn btn-secondary btn-sm btn-block" onclick="excel_modal.group();" data-dismiss="modal">
                    <i class="fas fa-boxes"></i>
                    <span class="ml10 mr10">Добавить / обновить группы товаров с Excel</span>
                    <i class="fas fa-boxes"></i>
                  </button>
                  <!-- <button class="btn btn-secondary btn-sm btn-block" onclick="excel_modal.group_discount();" data-dismiss="modal">
                    <i class="fas fa-percent"></i>
                    <span class="ml10 mr10">Добавить группы скидок с Excel</span>
                    <i class="fas fa-percent"></i>
                  </button> -->
                  <button class="btn btn-success btn-sm btn-block" onclick="add_m();" data-dismiss="modal">Добавить</button>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="add_catalog_groups" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
          <form onsubmit="return false;" id="add_catalog_groups_form">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить</h5>
              </div>
              <div class="modal-body">
                <label for="">Наименование</label>
                <input type="text" name="name" class="form-control" placeholder="Введите наименование">
                <label for="">Категории</label>
                <select class="form-control select2" name="category"><?php echo $category_opt;?></select>
                <label for="">Комментарий</label>
                <textarea name="comment" cols="30" rows="10" class="form-control" placeholder="Введите комментарий"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="add_m_e();" data-dismiss="modal">Создать</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="save_excel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
          <form onsubmit="return false;" id="save_excel_modal_form" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить</h5>
              </div>
              <div class="modal-body">
                <input type="file" class="form-control" name="file">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary save_excel_modal_btn" onclick="excel_modal.group_save();" data-dismiss="modal" id="save_excel_modal_btn_add">Добавить</button>
                <button type="button" class="btn btn-primary save_excel_modal_btn" onclick="excel_modal.group_discount_save();" data-dismiss="modal" id="save_excel_modal_btn_add_discount">Добавить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </form>
          </div>
        </div>
      </div>

  <?php

  $adminTpl->admin_foot();
}

function catalog_groups_edit($id){
  global $core;
  $data = \Catalog_v2\Model\CatalogGroups::findOrFail($id);

  // $catalog_groups_discount = catalog_groups_discount($id);

  // $tov_list = $CatalogGroups->get_tov($id);

  $discount_name_list = \Catalog_v2\Model\CatalogGroupsDiscountName::orderBy("position")->get();
  $discount_name_list_str = [];
  foreach($discount_name_list as $item){
    $discount_name_list_str[] = "<option value='{$item->id}'>{$item->name}</option>";
  }
  $discount_name_list_str = implode("", $discount_name_list_str);

  $catalog_v2 = $core->aCatList('catalog_v2');
  $opt = "<option></option>".array_to_options($catalog_v2, ["selected" => $data["category"]]);

  $table_data = catalog_groups_table($id);

  $paginate = [];
  for($i = 1; $i<=$table_data["data"]->lastPage();$i++){
    $active = $i == $table_data["data"]->currentPage() ? "paginate-item-active" : "";
    $paginate[] = <<<HTML
      <div class="paginate-item {$active}" data-page="{$i}">{$i}</div>
HTML;
  }
  $paginate = implode("", $paginate);

  return <<<HTML

    <style>
      #wrapper-table{
        overflow-x: auto;
        height: 700px;
      }
      thead th { 
        position: sticky; top: 0; 
        background: white;
      }
      #paginate{
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
      }
      .paginate-item {
        padding: 5px;
        min-width: 30px;
        border: 1px grey solid;
        text-align: center;
        cursor: pointer;
      }
      .paginate-item-active{
        background-color: black;
        color: white;
      }

    </style>

    <input type="hidden" value="{$id}" id="mainId">
    
    <div class="panel panel-dark panel-border top">
        <div class="panel-heading">

        </div>
        <div class="panel-body pn">
          <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3>Редактировать</h3>
                    <form class="form-horizontal" onsubmit="return false;" id="mainForm">
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="">Наименование:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" value="{$data['name']}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="">Категория:</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="category">{$opt}</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="category_radio">Комментарий:</label>
                            <div class="col-sm-9">
                                <textarea name="comment" cols="30" rows="10" class="form-control">{$data['comment']}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="button" value="Сохранить" id="submit" class="btn btn-success" onclick="update_m();">
                        </div>
                    </form>
                </div>
                <!-- <div class="col-md-4">
                    <h3>Группы скидок</h3>
                    <button type="button" class="btn btn-success btn-sm" onclick="discount_modal.add();">Добавить</button>
                    <div id="catalog_groups_discount" class="form-group">
                      
                    </div>
                </div> -->
            </div>
            <div class="row">
              <div class="col-sm-12">
                  <h3>Список товаров</h3>
                  <button type="button" id="copy-table" class="btn btn-info btn-xs">Скопировать таблицу</button>
                  <div id="wrapper-table">
                    <table class="table table-striped" id="main-table">
                      {$table_data["template"]}
                    </table>
                  </div>
                  <div id="paginate">{$paginate}</div>
              </div>
            </div>
          </div>
        </div>
    </div>
    
    

    <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form onsubmit="return false;" id="discountModalForm">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Группа скидок</h5>
            </div>
            <div class="modal-body">
                <label for="">Наименование</label>
                <select name="catalog_groups_discount_name_id" id="discount_name" class="form-control">
                  {$discount_name_list_str}
                </select>
                <label for="">Скидка</label>
                <input type="number" name="discount" id="discount_discount" class="form-control" placeholder="Введите скидку">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary discountModalBtn" id="discountModalBtnAdd" data-dismiss="modal" onclick="discount_modal.add_e();">Добавить</button>
              <button type="button" class="btn btn-primary discountModalBtn" id="discountModalEdit" data-dismiss="modal">Редактировать</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
          </form>
        </div>
      </div>
    </div>


HTML;
}

function catalog_groups_table($id){
  $thead = [];
  $thead_link = [];
  $tov_list = [];
  $list = \Catalog_v2\Model\Catalog::with("catalog_price_rival")->where("catalog_groups_id", $id)->paginate(1000);
  $i = 0;
  $thead[$i] = "<th>ID</th>";
  $thead_link["ID"] = $i;
  $i++;
  $thead[$i] = "<th>Товар</th>";
  $thead_link["Товар"] = $i;
  $i++;
  $thead[$i] = "<th>Ссылка на поставщика</th>";
  $thead_link["rival_link"] = $i;
  $i++;
  $thead[$i] = "<th>Ссылка на конкурента</th>";
  $thead_link["competing_links"] = $i;
  $i++;
  $thead[$i] = "<th>Тип</th>";
  $thead_link["Тип"] = $i;
  $i++;
  foreach($list as $l){
    if(!empty($l->price_data)){
      foreach($l->price_data["data"] as $pdd){
        if(!isset($thead_link[ $pdd["rus"] ])){
          $thead[ $i ] = "<th>{$pdd["rus"]}</th>";
          $thead_link[ $pdd["rus"] ] = $i;
          $i++;
          if($pdd["rus"] == "Прайс конкурента udmsnab.ru"){
            // dd($l);
          }
        }
      }
    }
  }
  $len = $i - 1;
  if($list->total() == 0){
    $tov_list[] = "<td>Товары отсутствуют!</td>";
  } else {
    foreach($list as $l){
      $ppp = [];
      $i = 0;
      $ppp[$i] = "<td>{$l->id}</td>";
      $i++;
      $ppp[$i] = "<td><a target=\"_blank\" href=\"/administration/module/catalog_v2/edit/{$l->id}\">{$l->title} {$l->sku}</a></td>";
      $i++;
      $rival_link = $l->catalog_price_rival->rival_link ?? null;
      $ppp[$i] = "<td>{$rival_link}</td>";
      $i++;
      $competing_links = $l->catalog_price_rival->competing_links ?? null;
      $ppp[$i] = "<td>{$competing_links}</td>";
      $i++;
      if(!empty($l->price_data)){
        $ppp[$i] = "<td>{$l->price_data['type']}</td>";
        $i++;
        foreach($l->price_data["data"] as $pd){
          $value = str_replace(".", ",", $pd['value']);
          $ppp[ $thead_link[ $pd["rus"] ] ] = "<td>{$value}</td>";
        }
      }
      for($i = 0;$i<=$len;$i++){
        if(!isset($ppp[ $i ])){
          $ppp[ $i ] = "<td>-</td>";
        }
      }
      ksort($ppp);
      $ppp = implode("", $ppp);
      $tov_list[] = <<<HTML
        <tr>
          {$ppp}
        </tr>
HTML;
    }
  }
  $thead = implode("", $thead);
  $tov_list = implode("", $tov_list);
  $template = <<<HTML
    <thead>
      <tr>
        {$thead}
      </tr>
    </thead>
    <tbody>{$tov_list}</tbody>
HTML;
  return [
    "template" => $template,
    "data" => $list,
  ];
}

function catalog_groups_discount($id){
  $data = \Catalog_v2\Model\CatalogGroupsDiscount::with("catalog_groups_discount_name")->where("catalog_groups_id", $id)->get()->toArray();
  if(count($data) > 0){
    $result = [];
    foreach($data as $val){
      $result[] = catalog_groups_discount_template($val);
    }
    $result = implode("", $result);
  } else {
    $result = '<i>Группы скидок отсутствуют</i>';
  }
  return <<<HTML
    
    {$result}

HTML;
}

function catalog_groups_discount_template($request){
  return <<<HTML
    <div>
      {$request["catalog_groups_discount_name"]["name"]} - {$request["discount"]}% 
        <div class="btn-group" role="group" aria-label="Basic example">
          <i class="fas fa-edit btn btn-sm text-primary" title="Редактировать" onclick="discount_modal.edit({id: '{$request['id']}', name_id: '{$request['catalog_groups_discount_name_id']}', discount: '{$request['discount']}' });"></i>
          <i class="fas fa-times btn btn-sm text-danger" title="Удалить" onclick="del_m({$request['id']});"></i>
        </div>
    </div>
HTML;
}

function catalog_groups_download_excel(){
    $data = \Catalog_v2\Model\CatalogGroups::all()->toArray();
    $file = "CatGroups_" . time();
  
    $new_data = [];
    foreach($data as $d){
      $new_data[] = [
        $d["id"], 
        $d["name"], 
        $d["category"], 
        $d["comment"], 
      ];
    }
  
    SpreadsheetSupport::data_to_excel([
      "filename" => $file,
      "head" => [
        "id {%id%}",
        "Наименование {%name%}",
        "Категория {%category%}",
        "Комментарий {%comment%}",
      ],
      "data" => $new_data,
    ]);
}

function catalog_groups_save_excel($request){
  $file = $request["file"];

  $excel = new SpreadsheetSupport;
  $data = $excel->get_parse_data($file);

  foreach($data as $item){
    $update = false;
    if(!empty($item["id"])){
      $find = \Catalog_v2\Model\CatalogGroups::find($item["id"]);
      if(!empty($find)){
        $update = $find["id"];
      }
    }
    if($update){
      \Catalog_v2\Model\CatalogGroups::findOrFail($update)->update($item);
    } else {
      \Catalog_v2\Model\CatalogGroups::create($item);
    }
  }
}

function catalog_groups_discount_save_excel($request){
  require_once("usr/plugins/PHPExcel.php");
  $file = $request["file"];
  $excel = PHPExcel_IOFactory::load($file["tmp_name"]);
  $countRow =  $excel->setActiveSheetIndex(0)->getHighestRow(); //количество строк
  $countColumnAlp =  $excel->setActiveSheetIndex(0)->getHighestColumn(); //количество столбцов в буквенном виде
  $countColumn = PHPExcel_Cell::columnIndexFromString($countColumnAlp) - 1; //количество столбцов в числовом виде с 0

  $array_name = [];
  for($i = 0; $i <= $countColumn; $i++){
      $arrayRes = [];
      $str =  $excel->getActiveSheet()->getCellByColumnAndRow($i, 1)->getValue();
      preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
      if(empty($arrayRes[0])){
          $array_name[$i] = null;
      } else {
          $array_name[$i] = str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
      }
  }

  for ($i = 2; $i <= $countRow; $i++) {
    //проход по столбцам
    $add = [];
    $catalog_groups_id = false;
    for($j = 0; $j<= $countColumn; $j++){
        $flag_no_empty = true;
        $val = $excel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getFormattedValue();
        if(empty($array_name[$j])){
            continue;
        }
        if(empty($val) && $val !== 0 && $val !== "0"){
            $flag_no_empty = false;
        }
        $val = trim($val);
       if(strstr($array_name[$j], "discount_")){
          if($flag_no_empty){
            $num = explode("_", $array_name[$j]);
            if(isset($num[1])){
              $add[ $j ]["discount"] = $val;
              $add[ $j ]["catalog_groups_discount_name_id"] = $num[1];
            }
          }
        } else if($array_name[$j] === 'catalog_groups_id'){
          if($flag_no_empty){
            $catalog_groups_re = $catalog_groups->findName($val);
            if(count($catalog_groups_re) > 0){
              $catalog_groups_id = $catalog_groups_re["id"];
            }
          }
        } else {
          // if($flag_no_empty){
          //     $add[ $array_name[$j] ] = htmlspecialchars($val, ENT_QUOTES);
          // }
        }
    }

    if($catalog_groups_id){
      //add
      foreach($add as $v){
        $add_ = $v;
        $add_["catalog_groups_id"] = $catalog_groups_id;
        // dd($add_);
        \Catalog_v2\Model\CatalogGroupsDiscount::create($add_);
      }
    }
  }
}