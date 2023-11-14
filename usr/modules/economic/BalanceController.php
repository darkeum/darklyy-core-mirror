<?php

namespace Economic;

use \Boot\Support\Facades\Auth;

class BalanceController{
    public function grid(){
        global $adminTpl;
        $lnn = '';
        $adminTpl->admin_head();
        $atributs = array();
        $atributs['opts'] = 'open';
        $atributs['module'] = 'module';
        $atributs['module_name'] = 'economic/balance';
        $atributs['module_open'] = 'editor';
        //$atributs['js'] = 'setTimeout(function () {$("#jqSales").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSales\').trigger(\'reloadGrid\');}, 150);';
        loadGrid('jqBalance', $lnn, $atributs, ROOT.'/usr/modules/economic/admin/grid/');

        //$adminTpl->footIncludes[] = '<script src="/usr/modules/***"></script>';
        $adminTpl->admin_foot();
    }

    public function editor(){
        global $adminTpl;
        if(empty($this->oid)) throw new \Exception("Field `oid` is empty!");

        $org_option = [];
        $first_org = null;
        foreach(\Organizations\Model\Org::all() as $item){
          if(is_null($first_org)) $first_org = $item->id;
          $org_option[] = "<option value='{$item->id}'>{$item->organization->name}</option>";
        }


        $org_option = implode("", $org_option);
        $reestr_opt = static::reestr_opt($first_org, $this->oid);

        $oid_data = \Organizations\Model\Organizations::find($this->oid);

        $adminTpl->admin_head();
        echo <<<HTML
            <div class="container admin-form">
                <div class="row mt20">
                  <div class="col-sm-12">
                    <div class="panel">
                      <div class="panel-body"><b>{$oid_data->name}</b></div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel">
                            <div class="panel-body" id="view_list">{$this->view_list()}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="panel">
                            <div class="panel-body">
                                <form class="form-horizontal" id="mainForm">
                                    <div class="form-group">
                                      <label class="control-label col-sm-2">Организация:</label>
                                      <div class="col-sm-6">
                                        <select name="org" id="org_select" class="form-control">{$org_option}</select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-sm-2">Договор:</label>
                                      <div class="col-sm-6">
                                        <select name="reestr_id" id="reestr_select" class="form-control">
                                          {$reestr_opt}
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-sm-2">Дата:</label>
                                      <div class="col-sm-6">
                                        <input type="date" class="form-control" name="date" required>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-sm-2">Сумма:</label>
                                      <div class="col-sm-6">
                                        <input type="number" class="form-control" name="balance" step="any" required>
                                      </div>
                                    </div>
                                    <input type="hidden" name="oid" value="{$this->oid}">
                                    <input type="submit" value="Добавить" class="btn btn-success">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
              $(function(){
                  delete_icon_init();
                  $("#mainForm").on("submit", async function(e){
                    e.preventDefault();
                    const data = $(this).serialize();
                    const lw = new LW;
                    lw.on();
                    await send(data);
                    await load_list();
                    $(this)[0].reset();
                    lw.off();
                  });

                  $("#org_select").on("change", async function(){
                      const lw = new LW;
                      lw.on();
                      await reestr_options($(this).val());
                      lw.off();
                  });

                  async function reestr_options(org){
                    const data = await(await fetch(`/administration/module/economic/balance/reestr_options/\${org}/{$this->oid}`)).text();
                    $("#reestr_select").html(data);
                  }

                  async function send(data){
                    await $.post("/administration/module/economic/balance/create", data);
                  }

                  async function load_list(){
                    const list = await ( await fetch("/administration/module/economic/balance/view_list/{$this->oid}") ).text();
                    $("#view_list").html(list);
                    delete_icon_init();
                  }

                  function delete_icon_init(){
                    $(".delete_icon").on("click", function(){
                      swal({
                        title: "Вы уверены?",
                        text: "Удалить начальное сальдо",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                      })
                      .then(async (willDelete) => {
                        if (willDelete) {
                            const lw = new LW;
                            lw.on();
                            const id = $(this).data("id");
                            await (await fetch(`/administration/module/economic/balance/delete/\${id}`)).text();
                            await load_list();
                            lw.off();
                        }
                      });
                    });
                  }
              })

            </script>
HTML;
        $adminTpl->admin_foot();
    }

    static function reestr_opt($org, $oid){
      $reestr_opt = ["<option value=''>Не указан</option>"];
      $reestrs = \Reestr\Models\Reestr::where("oid", $oid)->where("org", $org)->get();
      foreach($reestrs as $reestr){
        $title = ActController::contract_title($reestr)." ({$reestr->id})";
        $reestr_opt[] = "<option value='{$reestr->id}'>{$title}</option>";
      }
      return implode("", $reestr_opt);
    }

    public function view_list(){
      $template = [];
      $items = \Economic\Model\Balance::where("oid", $this->oid)->where("action", "set")->orderBy("date")->get();

      if($items->count() > 0){
        foreach($items as $item){
          $template[] = $this->view_list_item($item);
        }
      } else {
        $template[] = "Сальдо начальное пусто!";
      }

      return implode("", $template);
    }

    public function view_list_item($item){
      $icon_action = "";
      switch($item->action){
        case "set":
          $icon_action = '<i class="fas fa-wrench text-primary" title="Установка значения"></i>';
        break;
        case "plus":
          $icon_action = '<i class="fas fa-plus text-success" title="Добавление"></i>';
        break;
        case "minus":
          $icon_action = '<i class="fas fa-minus text-danger" title="Уменьшение"></i>';
        break;
      }
      $reestr = $item->reestr;
      $contract_text = \Economic\ActController::contract_title($reestr);
      if(empty($reestr)){
        $contract = $contract_text;
      } else {
        $contract = "<a href='/administration/module/reestr/view/{$reestr->id}' target='_blank'>{$contract_text}</a>";
      }
      return <<<HTML
        <div style="border-bottom: 1px #DDD solid;" class="pb10 mb20 d-flex justify-content-between">
          <div>
            <div>Организация: {$item->org_->organization->name}</div>
            <div>Договор: {$contract}</div>
            <div>Дата: {$item->date->format("d.m.Y")}</div>
            <div>Сальдо: {$item->balance}</div>
            <div>Тип: {$icon_action}</div>
          </div>
          <div>
            <i class="fas fa-times cp text-danger delete_icon" data-id="{$item->id}"></i>
          </div>
        </div>
HTML;
    }

    public function create($request){
      $balance = new \Economic\Model\Balance;
      $balance->fill($request);
      $balance->reestr_id = empty($request["reestr_id"]) ? null : $request["reestr_id"];
      $balance->uid = Auth::id();
      $balance->action = "set";
      $balance->save();
      $BalanceRecount = new BalanceRecount;
      $BalanceRecount->action = "set";
      $BalanceRecount->init_from_balance($balance);
      $BalanceRecount->recount();
    }

    public function delete($id){
      $balance = \Economic\Model\Balance::find($id);
      $balance->delete();
      $BalanceRecount = new BalanceRecount;
      $BalanceRecount->init_from_balance($balance);
      $BalanceRecount->recount();
    }
}