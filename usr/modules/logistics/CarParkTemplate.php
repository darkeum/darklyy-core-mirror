<?php

namespace Logistics;

class CarParkTemplate{

    static $lnn = "Автопарк";

    static function grid(){
        global $adminTpl;
	    
	    $adminTpl->admin_head('Логистика | '.static::$lnn);
	    $atributs = array();
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = 'logistics/car_park';
	    $atributs['module_open'] = '';
	    loadGrid('jqCarPark', static::$lnn, $atributs, ROOT.'/usr/modules/logistics/admin/grid/');
        echo <<<HTML
            <script>
                function delete_car(id){
                    swal({
                      title: "Вы уверены?",
                      text: "Удалить машину",
                      icon: "warning",
                      buttons: true,
                      dangerMode: true,
                    })
                    .then(async (willDelete) => {
                      if (willDelete) {
                            await (await fetch(`/administration/module/logistics/car_park/destroy/\${id}`)).text();
                            \$grid.trigger('reloadGrid');
                      }
                    });
                }
            </script>
HTML;
        $adminTpl->admin_foot();
    }

    static function create(){
        global $adminTpl;
        $adminTpl->admin_head('Логистика | '.static::$lnn);

        $select_opt = [];
        foreach(\Organizations\Model\Org::all() as $item){
            $select_opt[] = "<option value='{$item->id}'>{$item->organization->name}</option>";
        }
        $select_opt = implode("", $select_opt);

        echo <<<HTML
            <div id="content" class="animated fadeIn">
                <div class="panel panel-dark panel-border top">
			        <div class="panel-body admin-form">
			        	<form id="admin-form" action="/administration/module/logistics/car_park/store" method="post" class="form-horizontal parsley-form admin-form">	
			        		<div class="form-group">
			        			<label for="" class="col-lg-3 control-label">Марка машины</label>
			        			<div class="col-lg-4"><input type="text" class="form-control" name="brand" required></div>
                            </div>
			        		<div class="form-group">
			        			<label for="" class="col-lg-3 control-label">Номер машины</label>
			        			<div class="col-lg-4"><input type="text" class="form-control" name="number" required></div>
                            </div>
			        		<div class="form-group">
			        			<label for="" class="col-lg-3 control-label">Организация</label>
			        			<div class="col-lg-4"><select name="org" class="form-control">{$select_opt}</select></div>
                            </div>
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </form>
                    </div>
                </div>
            </div>
HTML;
        $adminTpl->admin_foot();
    }
}