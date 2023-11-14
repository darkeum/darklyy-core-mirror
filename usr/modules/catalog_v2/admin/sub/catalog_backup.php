<?php

use \Catalog_v2\Model\CatalogBackup;
use \Catalog_v2\Model\Catalog;
use \Boot\Support\Str;

function catalog_backup(){
    global $adminTpl, $url;
    switch(isset($url[4]) ? $url[4] : null) {
        default:
            $lnn = 'Менеджер каталога';
            $adminTpl->admin_head('Менеджер каталога | '.$lnn);
            $atributs = array();
            $atributs['opts'] = 'open';
            $atributs['module'] = 'module';
            $atributs['module_name'] = 'catalog_v2/backup';
            $atributs['module_open'] = 'view';	
            //load_grid('jqCatalog', $lnn, $atributs);
            load_grid('jqCatalogBackup', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');
            $adminTpl->admin_foot();
        break;
        case "view":
            catalog_backup_view($url[5]);
        break;
        case "view_body":
            echo catalog_backup_view_body($url[5]);
        break;
        case "restore":
            catalog_backup_restore($url[5]);
        break;
    }
}

function catalog_backup_view($cid){
    global $adminTpl, $url;
    $adminTpl->admin_head();
    $body = catalog_backup_view_body($cid);
    echo <<<HTML
        <style>
            table {
                table-layout: fixed;
                word-wrap: break-word;
            }
        </style>
        <input type="hidden" value="{$cid}" id="mainId">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">№</th>
                                    <th style="width: 1%;">ID</th>
                                    <th style="width: 3%;">Автор</th>
                                    <th style="width: 2%;">Дата</th>
                                    <th style="width: 10%;">Было</th>
                                    <th style="width: 10%;">Стало</th>
                                    <th style="width: 5%;">Комментарий</th>
                                    <th style="width: 5%;">Действия</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">{$body}</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Восстановление</h5>
              </div>
              <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Сейчас</th>
                                <th>Станет</th>
                            </tr>
                        </thead>
                        <tbody id="restoreModalTBody"></tbody>
                    </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="restoreModalBtn">Восстановить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
HTML;
    $adminTpl->footIncludes[] = "<script src='/usr/modules/catalog_v2/admin/asset/js/catalog_backup_view.js'></script>";
    $adminTpl->admin_foot();
}

function catalog_backup_view_body($cid){
    $model = CatalogBackup::where("cid", $cid)->orderByDesc("id");
    $catalog = Catalog::find($cid);
    $body = [];
    if($model->exists()){
        $items = $model->get();
        $i = 0;
        foreach($items as $item){
            $i++;
            $author = shortUserName($item->uid);
            $before = [];
            $after = [];
            foreach($item->changes as $key => $change){
                $original = $item->original;
                switch($key){
                    default:
                        $before[] = Catalog::translate_field($key).": <b>{$original[ $key ]}</b>";
                        $after[] = Catalog::translate_field($key).": <b>{$change}</b>";
                    break;
                    case "updated_at":
                    break;
                }
            }
            $before = implode("<br><br>", $before);
            $after = implode("<br><br>", $after);
            $data = [];
            foreach($catalog->toArray() as $kc => $vc){
                $vcc = is_array($vc) ? json_encode($vc, JSON_UNESCAPED_UNICODE) : $vc;
                $data[] = [
                    "field" => $kc,
                    "rus" => Catalog::translate_field($kc),
                    "original" => $item->original[ $kc ] ?? $vcc,
                    "catalog" => $vcc,
                ];
            }
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $body[] = <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$item->id}</td>
                    <td>{$author}</td>
                    <td>{$item->created_at->format("d.m.Y H:i:s")}</td>
                    <td>{$before}</td>
                    <td>{$after}</td>
                    <td>{$item->comment}</td>
                    <td>
                        <button class="btn btn-success btn-xs" onclick='restore_modal({$item->id}, this);'>Восстановить</button>
                        <input type="hidden" value='{$data}' class="data">
                    </td>
                </tr>
HTML;
        }
    } else {
        $body[] = "<tr>
            <td colspan='8'>Изменения отсутствуют</td>
        </tr>";
    }
    return implode("", $body);
}

function catalog_backup_restore($id){
    $CatalogBackup = CatalogBackup::find($id);
    $backup = $CatalogBackup->original;
    $catalog = Catalog::find($CatalogBackup->cid);
    $catalog->change_catalog_backup_comment("Восстановление из backup №{$id}");
    foreach($backup as $field => $value){
        switch($field){
            default:
                if(Str::isJson($value)) $value = json_decode($value, true);
                $catalog->{$field} = $value;
            break;
            case "id":
            case "created_at":
            case "updated_at":
            break;
        }
    }
    // dd($catalog);
    $catalog->save();
}