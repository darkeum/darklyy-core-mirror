<?php

namespace Profile;

class PresentTemp{
    public function grid(){
        global $adminTpl;
	    $lnn = 'Xd';
	    $adminTpl->admin_head($lnn.' | '.$lnn);
	    $atributs = [];
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = 'profile';
        $atributs['module_open'] = 'view';
        loadGrid('jqPresent', $lnn, $atributs, ROOT.'/usr/modules/profile/admin/grid/');
        $adminTpl->admin_foot();
    }

    public function view_present($id){
        $opt = \Profile\Model\Present::getPresents();
        $pd = \Profile\Model\Present::find($id);
        $data = empty($pd->data) ? [] : $pd->data;
        $result = [];
        $i = 0;
        foreach($data as $item){
            $present_opt = array_to_options($opt, ["selected" => $item->present]);
            $result[] = <<<HTML
                <div class="form-group">
                    <div class="col-md-4 col-md-offset-3 pb10" style="border-bottom: 1px #dedede solid;">
                        <div>Количество</div>
                        <div><input type="number" value="{$item->count}" class="form-control" name="data[{$i}][count]" required></div>
                        <div class="mt10">Подарок</div>
                        <div><select name="data[{$i}][present]" class="form-control" required>{$present_opt}</select></div>
                        <div><button type="button" class="btn btn-xs btn-danger mt10" onclick="delete_item(this);">Удалить</button></div>
                    </div>
                </div>
HTML;
            $i++;
        }
        return implode("", $result);
    }

    public function view($id){
        global $adminTpl;
	    $lnn = 'Xd';
        $adminTpl->admin_head($lnn.' | '.$lnn);
        $pd = \Profile\Model\Present::find($id);
        $attText = '';
        $attArray = \Boot\Models\Attach::where("module", "present")->where("pid", $id)->get()->toArray();
        foreach ($attArray as $key => $value) { 
            $attText .= '<img width="150" src="'.$value['url'].'" />';
        }

        $fio = fullUserName($pd->uid);
        $present_opt = array_to_options(\Profile\Model\Present::getPresents());

        $active_checked = $pd->active == 1 ? "checked" : "";
        echo <<<HTML
            <div id="content" class="animated fadeIn">
			    <div class="panel panel-dark panel-border top">
			    	<div class="panel-heading"><span class="panel-title"></span>			
			    </div>			
			    <div class="panel-body admin-form">
			    	<form class="form-horizontal" method="POST" id="mainForm">
                        <div class="form-group">
			    			<label class="col-lg-3 control-label">Пользователь</label>
			    			<div class="col-lg-4">
                                {$fio}
                            </div>
                        </div>
                        <div class="form-group">
			    			<label class="col-lg-3 control-label">Изображение</label>
			    			<div class="col-lg-4">
                                {$attText}
                            </div>
                        </div>
                        <div class="form-group">
			    			<label class="col-lg-3 control-label">Разрешение</label>
			    			<div class="col-lg-4">
                                <input type="hidden" value="0" name="active">
                                <input type="checkbox" value="1" name="active" {$active_checked}>
                            </div>
                        </div>
                        <div class="form-group">
			    			<label class="col-lg-3 control-label">Количество выбираемых подарков</label>
			    			<div class="col-lg-4">
                                <input type="number" value="{$pd->count}" name="count" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Подарки</label>
                            <div class="col-lg-4">
                                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#presentModal">Добавить</button>
                                <button class="btn btn-xs btn-success">Сохранить</button>
                            </div>
                        </div>
                        <input type="hidden" name="data" value="[]">
                        <div id="view_present">{$this->view_present($id)}</div>

                    </form>
                </div>
            </div>

            <div class="modal fade" id="presentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="presentModalForm" method="POST">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Подарок</h5>
                        </div>
                        <div class="modal-body">
                            <div>
                                <div>Количество</div>
                                <div><input type="number" value="1" class="form-control" name="count" required></div>
                                <div>Подарок</div>
                                <div><select name="present" class="form-control" required>{$present_opt}</select></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button class="btn btn-primary">Добавить</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>

            <script>
                $(function(){
                    $("#mainForm").on("submit", async function(e){
                        e.preventDefault();
                        const lw = new LW;
                        lw.on();
                        await $.post("/administration/module/profile/save/{$id}", $(this).serialize());
                        lw.off();
                    });

                    $("#presentModalForm").on("submit", async function(e){
                        e.preventDefault();
                        $("#presentModal").modal("hide");
                        const lw = new LW;
                        lw.on();
                        await $.post("/administration/module/profile/add_present/{$id}", $(this).serialize());
                        await view_present();
                        lw.off();
                    });

                    async function view_present(){
                        const data = await $.post("/administration/module/profile/view_present/{$id}");
                        $("#view_present").html(data);
                    }
                })

                function delete_item(This){
                    $(This).parent().parent().remove();
                }
            </script>
HTML;
        $adminTpl->admin_foot();
    }

    public function view_present_selected_grid(){
        global $adminTpl;
	    $lnn = 'Xd';
	    $adminTpl->admin_head($lnn.' | '.$lnn);
	    $atributs = [];
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = 'profile';
        $atributs['module_open'] = 'view_present_selected';
        load_grid('jqPresent', $lnn, $atributs, ROOT.'/usr/modules/profile/admin/grid/');
        $adminTpl->admin_foot();
    }

    public function view_present_selected($id){
        global $adminTpl;
	    $lnn = 'Xd';
        $adminTpl->admin_head($lnn.' | '.$lnn);
        $pd = \Profile\Model\Present::find($id);
        $fio = fullUserName($pd->uid);
        echo <<<HTML
            <div id="content" class="animated fadeIn">
			    <div class="panel panel-dark panel-border top">
			    	<div class="panel-heading"><span class="panel-title"></span>			
			    </div>			
			    <div class="panel-body admin-form">
			    	<form class="form-horizontal" method="POST" id="mainForm">
                        <div class="form-group">
			    			<label class="col-lg-3 control-label">Пользователь</label>
			    			<div class="col-lg-4">
                                {$fio}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2">Выбранные подарки</div>
                        </div>
                        <div class="d-flex col-lg-offset-3">
			    			{$this->view_present_selected_items($id)}
                        </div>
                    </form>
                </div>
            </div>

            <script>
                
            </script>
HTML;
        $adminTpl->admin_foot();
    }

    public function view_present_selected_items($id){
        $result = [];
        $pd = \Profile\Model\Present::find($id);
        $data = empty($pd->data2) ? [] : $pd->data2;
        if(empty($data)){
            $result[] = "Подарки не выбраны";
        } else {
            $p_present = \Profile\Model\Present::getPresents();
            $p_image = \Profile\Model\Present::getPresentsImages();
            $p_selected = \Profile\Model\Present::getPresentsSelect();
            foreach($data as $item){
                $result[] = <<<HTML
                    <div>
                        <div>{$p_present[ $item->present ]}</div>
                        <div class="d-flex justify-content-center"><img src="{$p_image[ $item->present ]}" width="125"></div>
                        <div>{$p_selected[ $item->present ][ $item->type ]}</div>
                    </div>
HTML;
            }
        }
        return implode("", $result);
    }
}