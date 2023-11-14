<?php

namespace Organizations;

use Boot\System\Office\OfficeSupport;

class OrganizationsTemplate{
    static public function add($id = false){
        global $adminTpl, $core, $db, $lang;

        $org = [];
        $lnn = $lang['org_add'];

        if(($id)) {
            $org = \Organizations\Model\Organizations::find($id)->toArray();
            $lnn = $lang['org_edit'];
        }

        $inn =      isset($org['inn']) ? $org['inn'] : "";
        $yaddress = isset($org['yaddress']) ? $org['yaddress'] : "";
        $maddress = isset($org['maddress']) ? $org['maddress'] : "";
        $name =     isset($org['name_full']) ? $org['name_full'] : "";
        $sname =    isset($org['name']) ? $org['name'] : "";
        $post =     isset($org['post']) ? $org['post'] : "";
        $director = isset($org['director']) ? $org['director'] : "";
        $kpp =      isset($org['kpp']) ? $org['kpp'] : "";
        $ogrn =     isset($org['ogrn']) ? $org['ogrn'] : "";
        $director = isset($org['director']) ? $org['director'] : "";
        $dey =      isset($org['dey']) ? $org['dey'] : "";
        $phone =    isset($org['phone']) ? $org['phone'] : "";
        $email =    isset($org['email']) ? $org['email'] : "";
        $type =     isset($org['type']) ? $org['type'] : "";
        $type_ip = $type == "INDIVIDUAL";
        
        $adminTpl->admin_head($lang['organizations'] . ' | ' . $lnn);

        $validation_array = array(
            'inn' => array(
                'required' =>  array('true', $lang['org_inn_err']),
                'number' =>  array('true', $lang['org_err_number'])
            ),
            'name' => array(
                'required' =>  array('true', $lang['org_name_err'])
            ),
            'sname' => array(
                'required' =>  array('true', $lang['org_name_short_err'])
            ),
            'ogrn' => array(
                'required' =>  array('true', $lang['org_ogrn_err']),
                'number' =>  array('true', $lang['org_err_number'])
            ),
            'director' => array(
                'required' =>  array('true', $lang['org_director_err'])
            ), 
            'yaddress' => array(
                'required' =>  array('true', $lang['org_yadress_err_1'])
            ),
            'maddress' => array(
                'required' =>  array('true', $lang['org_madress_err_1'])
            ),
    
        );
        if(!$type_ip){
            $validation_array["post"] = array(
                'required' =>  array('true', $lang['org_post_err'])
            );
            $validation_array["dey"] = array(
                'required' =>  array('true', $lang['org_dey_err'])
            );
        }
        
        validationInit($validation_array);
        $adminTpl->js_code[] = '
            $("#inn_b").click(function(e) {
                var inn_text = document.getElementById("inn_text");
                var promise = suggest(inn_text.value);
                promise
                    .done(function(response) {
                      showParty(response.suggestions)
                      console.log(response);
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                      console.log(textStatus);
                      console.log(errorThrown);
                    });
                });			
                function suggest(query) {
                  var serviceUrl = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party",
                      token = "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e";
                  var request = {
                    "query": query
                  };
                  var params = {
                    type: "POST",
                    contentType: "application/json",
                    headers: {
                      "Authorization": "Token " + token
                    },
                    data: JSON.stringify(request)
                  }  
                  return $.ajax(serviceUrl, params);
                }
                function clearParty() {
                  $("#name_short").val("");
                  $("#name_full").val("");
                  $("#kpp").val("");
                  $("#address").val("");
                }
                function showParty(suggestions) {
                  clearParty();
                  if (suggestions.length === 0) return;
                  var party = suggestions[0].data;
                  $("#sname").val(party.name.short_with_opf);
                  $("#name").val(party.name.full_with_opf);
                  $("#yaddress").val(party.address.value);
                  $("#kpp").val(party.kpp); 
                  $("#ogrn").val(party.ogrn);
                  $("#post").val(party.management.post); 
                  $("#director").val(party.management.name);   
                  $("#type").val(party.type);   
                }';
        $adminTpl->js_code[] = '
                    function showSuggestion(suggestion) {
                      console.log(suggestion);
                      var data = suggestion.data;
                      if (!data)
                        return;
                      $("#bsname").val(data.name && data.name.payment || "");
                      $("#bname").val(data.name && data.name.full || "");
                      $("#bik").val(data.bic);
                      $("#kschet").val(data.correspondent_account);
                      $("#baddress").val(data.address.value);
                      $("#type").val(data.type);
                    }
                    $("#bank").suggestions({
                      token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
                      type: "BANK",
                      count: 5,
                      onSelect: showSuggestion
                    });';
        $adminTpl->footIncludes[] = '<link href="https://cdn.jsdelivr.net/jquery.suggestions/17.2/css/suggestions.css" type="text/css" rel="stylesheet" /><script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.suggestions/17.2/js/jquery.suggestions.min.js"></script>';
        echo '
        <input type="hidden" value="'.$id.'" id="mainId">
        <div id="content" class="animated fadeIn">
                <form id="admin-form" action="{MOD_LINK}/save" method="post" name="cats" role="form" class="form-horizontal parsley-form" autocomplete="off">		
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-info panel-border top">
                        <div class="panel-heading"><span class="panel-title">' . $lang['org_main'] . ':</span></div>
                        <div class="panel-body admin-form">								
                                <div class="form-group">
                                    <label for="inn" class="col-lg-3 control-label">' . $lang['org_inn'] . '</label>
                                    <div class="col-lg-9 smart-widget sm-right smr-90 ">
                                      <label class="field prepend-icon">
                                        <input id="inn_text"  autocomplete="off" type="text" name="inn" placeholder="' . $lang['org_inn_pre'] . '" class="gui-input" value=\'' . $inn . '\'>
                                        <label for="inn" class="field-icon"><i class="fa fa-search"></i></label>
                                      </label>
                                      <button type="button" id="inn_b" class="button btn-primary" style="margin-right: 11px;">Заполнить</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sname"  class="col-lg-3 control-label">' . $lang['org_name_short'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="sname" class="field prepend-icon">
                                            <input id="sname" type="text" name="sname" value=\'' . $sname . '\' placeholder="' . $lang['org_name_short_pre'] . '" class="gui-input">
                                            <label for="sname" class="field-icon"><i class="far fa-address-card"></i></label>
                                        </label>
                                    </div>
                                </div>		
                                <div class="form-group">
                                    <label for="name"  class="col-lg-3 control-label">' . $lang['org_name'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="name" class="field prepend-icon">
                                            <input id="name" type="text" name="name" value=\'' . $name . '\' placeholder="' . $lang['org_name_pre'] . '" class="gui-input">
                                            <label for="name" class="field-icon"><i class="fa fa-address-card"></i></label>
                                        </label>
                                    </div>
                                </div>		
                                <div class="form-group">
                                    <label for="yaddress"  class="col-lg-3 control-label">' . $lang['org_yadress'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="yaddress" class="field prepend-icon">
                                            <input id="yaddress" type="text" name="yaddress" value="' . $yaddress . '" placeholder="' . $lang['org_yadress_pre'] . '" class="gui-input">
                                            <label for="yaddress" class="field-icon"><i class="fa fa-map-marker"></i></label>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="maddress"  class="col-lg-3 control-label">' . $lang['org_madress'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="maddress" class="field prepend-icon">
                                            <input id="maddress" type="text" name="maddress" value="' . $maddress . '" placeholder="' . $lang['org_madress_pre'] . '" class="gui-input">
                                            <label for="maddress" class="field-icon"><i class="far fa-envelope"></i></label>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kpp"  class="col-lg-3 control-label">' . $lang['org_kpp'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="kpp" class="field prepend-icon">
                                            <input id="kpp" type="text" name="kpp" value="' . $kpp . '" placeholder="' . $lang['org_kpp_pre'] . '" class="gui-input">
                                            <label for="kpp" class="field-icon"><i class="fa fa-pencil "></i></label>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ogrn"  class="col-lg-3 control-label">' . $lang['org_ogrn'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="ogrn" class="field prepend-icon">
                                            <input id="ogrn" type="text" name="ogrn" value="' . $ogrn . '" placeholder="' . $lang['org_ogrn_pre'] . '" class="gui-input">
                                            <label for="ogrn" class="field-icon"><i class="fa fa-pencil "></i></label>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="post"  class="col-lg-3 control-label">' . $lang['org_post'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="post" class="field prepend-icon">
                                            <input id="post" type="text" name="post" value="' . $post . '" placeholder="' . $lang['org_post_pre'] . '" class="gui-input">
                                            <label for="post" class="field-icon"><i class="fa fa-suitcase"></i></label>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="director"  class="col-lg-3 control-label">' . $lang['org_director'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="director" class="field prepend-icon">
                                            <input id="director" type="text" name="director" value="' . $director . '" placeholder="' . $lang['org_director_pre'] . '" class="gui-input">
                                            <label for="director" class="field-icon"><i class="fa fa-user"></i></label>
                                        </label>
                                    </div>
                                </div>		
                                <div class="form-group">
                                    <label for="dey"  class="col-lg-3 control-label">' . $lang['org_dey'] . '</label>
                                    <div class="col-lg-9">
                                        <label for="dey" class="field prepend-icon">
                                            <input id="dey" type="text" name="dey" value="' . $dey . '" placeholder="' . $lang['org_dey_pre'] . '" class="gui-input">
                                            <label for="dey" class="field-icon"><i class="far fa-file-text"></i></label>
                                        </label>
                                    </div>
                                </div>						
                                <div class="form-group">
                                    <label for="dey"  class="col-lg-3 control-label">Тип контрагента</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="type" id="type">';
                                            $opt = [
                                                ["text" => "Не указано", "value" => ""],
                                                ["text" => "ИП", "value" => "INDIVIDUAL"],
                                                ["text" => "ООО", "value" => "LEGAL"],
                                            ];
                                            foreach($opt as $o){
                                                $selected = $o["value"] == $type ? "selected" : "";
                                                echo '<option value="'.$o["value"].'" '.$selected.'>'.$o["text"].'</option>';
                                            }
                                        echo '</select>
                                    </div>
                                </div>						
                            </div>
                        </div>
                </div>	
                <div class="col-md-6">
                    <div class="panel panel-info panel-border top">
                        <div class="panel-heading"><span class="panel-title">' . $lang['org_bank'] . ':</span></div>
                    <div class="panel-body admin-form">	
                    <div class="form-group">
                        <div class="col-lg-12 text-right">
                            <input type="button" value="Добавить" class="btn btn-success" onclick="modalAddBD();" data-id="">
                        </div>
                    </div>				
    
                    <div class="form-group">
                        <table class="table" id="bankListTable">
                        <thead>
                            <tr>
                                <th>Банк</th>
                                <th colspan="2">Действия</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="bankList">
                            
                            </tbody>
                        </table>
                    </div>		
    
                    <div class="modal fade" id="modalAddBD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Банковские данные</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container-fluid">
                                        <div class="form-group">
                                        <label for="inn" class="col-lg-3 control-label">' . $lang['search'] . '</label>
                                            <div class="col-lg-9">
                                                <label class="field prepend-icon">
                                                    <input id="bank" name="bank" type="text" placeholder="' . $lang['org_bank_serach'] . '" class="gui-input">
                                                    <label for="inn" class="field-icon"><i class="fa fa-search"></i></label>
                                                </label>                            
                                            </div>
                                    </div>	
                                    <hr class="short alt" />						
                                    <div class="form-group">
                                            <label for="bsname"  class="col-lg-3 control-label">' . $lang['org_name_short'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="bsname" class="field prepend-icon">
                                                    <input id="bsname" type="text" name="bsname" value="" placeholder="' . $lang['org_name_short_b_pre'] . '" class="gui-input">
                                                    <label for="bsname" class="field-icon"><i class="fa fa-building-o"></i></label>
                                                </label>
                                            </div>
                                    </div>		
                                    <div class="form-group">
                                            <label for="bname"  class="col-lg-3 control-label">' . $lang['org_name'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="bname" class="field prepend-icon">
                                                    <input id="bname" type="text" name="bname" value="" placeholder="' . $lang['org_name_b_pre'] . '" class="gui-input">
                                                    <label for="bname" class="field-icon"><i class="fa fa-building"></i></label>
                                                </label>
                                            </div>
                                    </div>		
                                    <div class="form-group">
                                            <label for="baddress"  class="col-lg-3 control-label">' . $lang['org_badress'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="baddress" class="field prepend-icon">
                                                    <input id="baddress" type="text" name="baddress" value="" placeholder="' . $lang['org_badress_pre'] . '" class="gui-input">
                                                    <label for="baddress" class="field-icon"><i class="fa fa-map-marker"></i></label>
                                                </label>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label for="bik"  class="col-lg-3 control-label">' . $lang['org_bik'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="bik" class="field prepend-icon">
                                                    <input id="bik" type="text" name="bik" value="" placeholder="' . $lang['org_bik_pre'] . '" class="gui-input">
                                                    <label for="bik" class="field-icon"><i class="fa fa-bank"></i></label>
                                                </label>
                                            </div>
                                    </div>						
                                    <div class="form-group">
                                            <label for="kschet"  class="col-lg-3 control-label">' . $lang['org_kschet'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="kschet" class="field prepend-icon">
                                                    <input id="kschet" type="text" name="kschet" value="" placeholder="' . $lang['org_kschet_pre'] . '" class="gui-input">
                                                    <label for="kschet" class="field-icon"><i class="fa fa-newspaper-o "></i></label>
                                                </label>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label for="rschet"  class="col-lg-3 control-label">' . $lang['org_rschet'] . '</label>
                                            <div class="col-lg-9">
                                                <label for="rschet" class="field prepend-icon">
                                                    <input id="rschet" type="text" name="rschet" value="" placeholder="' . $lang['org_rschet_pre'] . '" class="gui-input">
                                                    <label for="rschet" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                                </label>
                                            </div>
                                    </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" onclick="modalAddBDBtn(this);" id="modalAddBDBtn1" data-id="">Добавить</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal fade" id="modalRemoveBD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Удалить банковские данные</h5>
                                    </div>
                                    <div class="modal-body">
                                        Вы действительно хотите удалить банковские данные?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="modalRemoveBDBtn">Удалить</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                        ';
    
        echo '
                    <div class="panel panel-info panel-border top">
                        <div class="panel-heading"><span class="panel-title">Контактные лица:</span></div>
                        <div class="panel-body admin-form">	
                            <div class="form-group">
                                <div class="col-lg-12 text-right">
                                    <input type="button" value="Добавить" class="btn btn-success" onclick="addContactFaces();">
                                </div>
                            </div>	
                            <div class="form-group">
                                <table class="table" id="">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>E-mail</th>
                                        <th>Телефон</th>
                                        <th>Доп телефон</th>
                                        <th>Должность</th>
                                        <th colspan="3">Действия</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="CFTBody">
                                
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    
                    <div class="modal fade" id="viewCommentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
                            </div>
                            <div class="modal-body" id="viewCommentModalBody"> 
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="modal fade" id="addContactFacesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Добавить контактное лицо</h5>
                                </div>
                                <div class="modal-body admin-form">
                                    <div class="container-fluid">
                                    <div class="form-group">
                                            <label for="surnameCF"  class="col-lg-3 control-label">Поиск</label>
                                            <div class="col-lg-9">';
                                                // <select id="select2">';
                                                //     $qq = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE active = 1 AND hidden = 0");
                                                //     echo '<option value="-1" selected disabled>Выберите контрагента, если он существует</option>';
                                                //     while($r = $db->getRow($qq)){
                                                //         echo '<option value="'.$r["id"].'">'.fullUserName($r["id"]).'</option>';
                                                //     }
                                                // echo '</select>
                                                $select = [
                                                    "mod" => "jsAdminTpl",
                                                    "name" => "select2",
                                                    "ajaxUrl" => "getUsersAllToSelect",
                                                ];
                                                oSelectCustom($select);
                                                $select["mod"] = "select";
                                                oSelectCustom($select);
                                            echo '</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="surnameCF"  class="col-lg-3 control-label">Фамилия</label>
                                            <div class="col-lg-9">
                                                <label for="test" class="field prepend-icon">
                                                    <input id="surnameCF" type="text" name="surnameCF" value="" placeholder="Введите фамилию" class="gui-input" autocomplete="off">
                                                    <label for="surnameCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="nameCF"  class="col-lg-3 control-label">Имя</label>
                                            <div class="col-lg-9">
                                                <label for="test" class="field prepend-icon">
                                                    <input id="nameCF" type="text" name="nameCF" value="" placeholder="Введите имя" class="gui-input" autocomplete="off">
                                                    <label for="nameCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                        <label for="ochestvoCF"  class="col-lg-3 control-label">Отчество</label>
                                        <div class="col-lg-9">
                                            <label for="test" class="field prepend-icon">
                                                <input id="ochestvoCF" type="text" name="ochestvoCF" value="" placeholder="Введите отчество" class="gui-input" autocomplete="off">
                                                <label for="ochestvoCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="emailCF"  class="col-lg-3 control-label">E-mail</label>
                                        <div class="col-lg-9">
                                            <label for="test" class="field prepend-icon">
                                                <input id="emailCF" type="email" name="emailCF" value="" placeholder="Введите e-mail" class="gui-input" autocomplete="off">
                                                <label for="emailCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phoneCF"  class="col-lg-3 control-label">Телефон</label>
                                        <div class="col-lg-9">
                                            <label for="test" class="field prepend-icon">
                                                <input id="phoneCF" type="tel" name="phoneCF" value="" placeholder="Введите телефон" class="gui-input" autocomplete="off">
                                                <label for="phoneCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone2CF"  class="col-lg-3 control-label">Доп телефон</label>
                                        <div class="col-lg-9">
                                            <label for="test" class="field prepend-icon">
                                                <input id="phone2CF" type="tel" name="phone2CF" value="" placeholder="Введите доп телефон, если есть" class="gui-input" autocomplete="off">
                                                <label for="phone2CF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="typeCF"  class="col-lg-3 control-label">Назначение</label>
                                        <div class="col-lg-9">
                                            <label for="typeCF" class="field prepend-icon">
                                                <select id="typeCF" name="typeCF">';
                                                    $typeCF = getTypeOrg();
                                                    foreach($typeCF as $key => $val){
                                                        echo '<option value="'.$key.'">'.$val.'</option>';
                                                    }
                                                echo '</select>
                                                <label for="typeCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="postCF"  class="col-lg-3 control-label">Должность</label>
                                        <div class="col-lg-9">
                                            <label for="postCF" class="field prepend-icon">
                                                <input id="postCF" type="text" name="postCF" value="" placeholder="Введите должность" class="gui-input" autocomplete="off">
                                                <label for="postCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="postCF"  class="col-lg-3 control-label">Комментарий</label>
                                        <div class="col-lg-9">
                                            <label for="commentCF" class="field prepend-icon">
                                                <textarea id="commentCF" name="commentCF" placeholder="Введите комментарий" class="gui-input" autocomplete="off"></textarea>
                                                <label for="commentCF" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
                                            </label>
                                        </div>
                                    </div>
    
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="addContactFacesModalBtn" data-context="" data-id="">Добавить</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                </div>
                            </div>
                        </div>
                    </div>';
    
                    ?>
    
    
                    <div class="panel panel-info panel-border top">
                        <div class="panel-heading"><span class="panel-title">Договор по умолчанию:</span></div>
                        <div class="panel-body admin-form">	
                            <div class="form-group">
                                <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>№</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if($id){
                                        $query = $db->query("SELECT r.*, rt.short as rt_short FROM ".DB_PREFIX."_reestr as r LEFT JOIN ".DB_PREFIX."_reestr_type as rt ON(rt.id = r.type) WHERE r.oid = '".$id."'");
                                        
                                        if($db->numRows($query) > 0){
                                            self::radio_reestr_template(["id" => "", "rt_short" => "", "numb" => ""], $org["reestr_id"]);
                                            while($reestr = $db->getRow($query)){
                                                self::radio_reestr_template($reestr, $org["reestr_id"]);
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    
                    <?php
    
                    echo '<div class="panel panel-info panel-border top">
                        <div class="panel-heading"><span class="panel-title">' . $lang['org_contact'] . ':</span></div>
                        <div class="panel-body admin-form">	
                            <div class="form-group">
                                <label for="phone"  class="col-lg-3 control-label">' . $lang['org_phone'] . '</label>
                                <div class="col-lg-9">
                                    <label for="phone" class="field prepend-icon">
                                        <input id="phone" type="text" name="phone" value="' . $phone . '" placeholder="' . $lang['org_phone_pre'] . '" class="gui-input">
                                        <label for="phone" class="field-icon"><i class="fa fa-phone"></i></label>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email"  class="col-lg-3 control-label">' . $lang['org_email'] . '</label>
                                <div class="col-lg-9">
                                    <label for="email" class="field prepend-icon">
                                        <input id="email" type="text" name="email" value="' . $email . '" placeholder="' . $lang['org_email_pre'] . '" class="gui-input">
                                        <label for="email" class="field-icon"><i class="fa fa-envelope"></i></label>
                                    </label>
                                </div>
                            </div>	
                        </div>	
                    </div>
                </div>';
        if (($id)) {
            echo '
                                        <script>
                                            // $(\'#admin-form\').submit(function (e) {
                                            //     var form = this;
                                            //     window.change_field = false;
                                            //     e.preventDefault();
                                            //     setTimeout(function () {
                                            //         form.submit();
                                            //     }, 1000); // in milliseconds
                                            // });
                                            </script>
                                        <input type="hidden" name="edit" value="1">
                                        <input type="hidden" name="id" value="' . $id . '">';
        }
    
    
        echo '	</div>
        <div class="row">
                <div class="col-md-12">
        <div class="form-group">
                                    
                                    <div class="col-sm-12 text-center">
                                        <input type="submit" class="btn btn-success btn-parsley" id="sub" value="' . (isset($id) ?  $lang['org_save_edit'] : $lang['org_save_add']) . '" />
                                    </div>
                                </div>
                            </div>
                                </div>
        </form>
    </div>
            ';
        $adminTpl->footIncludes[] = '<script src="/usr/modules/organizations/admin/asset/js/index.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<link href="/usr/modules/organizations/admin/asset/css/index.css?'.time().'" rel="stylesheet">';
        $adminTpl->admin_foot();
    }

    static public function radio_reestr_template($reestr, $reestr_id){
        $radio_name = "radio_reestr";
        $id = $reestr["id"];
        $l_radio_name = $radio_name."_".$id;
        
        ?>
            <tr>
                <td>
                    <input 
                        type="radio" 
                        value="<?php echo $id;?>" 
                        name="<?php echo $radio_name;?>" 
                        id="<?php echo $l_radio_name;?>" 
                        <?php echo $reestr_id == $id ? "checked" : "";?>
                    />
                </td>
                <td>
                    <label for="<?php echo $l_radio_name;?>" style="width: 100%;">
                        <a href="/administration/reestr/view/<?php echo $id;?>"><?php echo $id;?></a>
                    </label>
                </td>
                <td>
                    <label for="<?php echo $l_radio_name;?>" style="width: 100%;">
                        <?php echo empty($reestr["rt_short"]) && empty($reestr["numb"]) ? "" : $reestr["rt_short"]."-".$reestr["numb"];?>
                    </label>
                </td>
            </tr>
        <?php
    }

    static public function update_db(){
        $org = \Organizations\Model\Organizations::whereNull("type")->get();
		foreach($org as $model){
			$token = 'ddeafb9ff43e79578243d8f031f6e9d8b047ca0e';
			$url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party?token=' . $token . '&query=' . $model->inn;
			$json = file_get_contents($url);
			$data = json_decode($json, true);
			if (isset($data['suggestions'][0]['data'])) {
				$model->type = $data['suggestions'][0]['data']['type'];
				if ($data['suggestions'][0]['data']['type'] == "INDIVIDUAL") {
					$name = $data['suggestions'][0]['data']['name']['full'];
					$dadata = new dadata();
					$result = $dadata->clean('name', $name);
					$model->director = $name;
					$model->director_detail = json_encode($result, JSON_UNESCAPED_UNICODE);
				}
				$model->save();
			}
		}
    }

    static public function getDataUser($request){
        global $db;
        $id = $request["id"] ?? '';
		$uid = $request["uid"] ?? '';
        $uu = false;
		if(!empty($id)){
			$uu = $db->query("SELECT u.*, uo.post, uo.comment as uocomment, uo.type FROM ".DB_PREFIX."_users_org as uo LEFT JOIN ".DB_PREFIX."_users as u ON(u.id = uo.uid) WHERE uo.oid = '".$id."'");
		}
		if(!empty($uid)){
			$uu = $db->query("SELECT u.*, uo.post, uo.comment as uocomment, uo.type FROM ".DB_PREFIX."_users as u LEFT JOIN ".DB_PREFIX."_users_org as uo ON(u.id = uo.uid) WHERE u.id = '".$uid."'");
		}
        if($uu){
            while($u = $db->getRow($uu)){
                $array[] = [
                    "data" => [
                        "surnameCF" => (empty($u["surname"]) ? "" : htmlspecialchars_decode($u["surname"], ENT_QUOTES)),
                        "nameCF" => (empty($u["name"]) ? "" : htmlspecialchars_decode($u["name"], ENT_QUOTES)),
                        "ochestvoCF" => (empty($u["ochestvo"]) ? "" : htmlspecialchars_decode($u["ochestvo"], ENT_QUOTES)),
                        "emailCF" => (empty($u["email"]) ? "" : htmlspecialchars_decode($u["email"], ENT_QUOTES)),
                        "phoneCF" => (empty($u["phone"]) ? "" : htmlspecialchars_decode($u["phone"], ENT_QUOTES)),
                        "phone2CF" => (empty($u["phone2"]) ? "" : htmlspecialchars_decode($u["phone2"], ENT_QUOTES)),
                        "typeCF" => (empty($u["type"]) ? "" : htmlspecialchars_decode($u["type"], ENT_QUOTES)),
                        "postCF" => (empty($u["post"]) ? "" : htmlspecialchars_decode($u["post"], ENT_QUOTES)),
                        "commentCF" => (empty($u["uocomment"]) ? "" : htmlspecialchars_decode($u["uocomment"], ENT_QUOTES)),
                        "id" => (empty($u["id"]) ? "" : $u["id"]),
                    ]
                ];
            }
            $json = json_encode($array, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    }

    static public function check_auth_group(){
        global $core;
        if ($core->auth->user_info['group'] == '6') {
            header('Location: /' . ADMIN . '/module/organizations/add');
        }
    }

    static public function save($request){
        global $adminTpl, $core, $lang;
		$adminTpl->admin_head($lang['organizations'] . ' | ' . $lang['org_save']);

        $id =           $request['id'] ?? 0;
		$inn =          $request['inn'] ?? '';
		$yaddress =     $request['yaddress'] ?? '';
		$maddress =     $request['maddress'] ?? '';
		$name_full =    $request['name'] ?? '';
		$name =         $request['sname'] ?? '';
		$kpp =          $request['kpp'] ?? '';
		$ogrn =         $request['ogrn'] ?? '';
		$post =         $request['post'] ?? '';
		$director =     $request['director'] ?? '';
		$reestr_id =    empty($request['radio_reestr']) ? null : $request['radio_reestr'];
		$type =         $request['type'] ?? null;

		$saveBank =     $request["saveBank"] ?? [];

		$phone =        $request['phone'] ?? '';
		$email =        $request['email'] ?? '';
		$dey =          $request['dey'] ?? '';
		$usersCF =      $request['usersCF'] ?? [];

        \Organizations\OrganizationsTemplate::check_auth_group();

        $organizations_data = [
            "name" => $name,
            "name_full" => $name_full,
            "yaddress" => $yaddress,
            "maddress" => $maddress,
            "inn" => $inn,
            "kpp" => $kpp,
            "ogrn" => $ogrn,
            "post" => $post,
            "director" => $director,
            "dey" => $dey,
            "phone" => $phone,
            "email" => $email,
            "reestr_id" => $reestr_id,
            "type" => $type,
        ];

		echo '<div id="content" class="animated fadeIn">';

        if(isset($request["edit"]) && $id != 0){
            $Organizations = \Organizations\Model\Organizations::find($id);
            if(!isset($Organizations->director_detail[0]["source"]) || $Organizations->director_detail[0]["source"] != $director){
                $result = static::get_direct_detail($director);
                $organizations_data["director_detail"] = $result;
            }
            $Organizations->update($organizations_data);
            $adminTpl->info($lang['org_edit_success'], 'info', null, $lang['info'], $lang['org_list'], ADMIN . '/module/organizations');

            $saveBankArray = self::save_organizations_bank($id, $saveBank);

            $arrayBankDel = [];
            $ttt = \Organizations\Model\OrganizationsBank::where("oid", $id)->get();
            foreach($ttt as $tt){
                if(!in_array($tt->id, $saveBankArray)){
					$arrayBankDel[] = $tt->id;
				}
            }

			if(count($arrayBankDel) > 0){
                \Organizations\Model\OrganizationsBank::find($arrayBankDel)->delete();
			}

            $qUsersOrg = \Boot\Models\UsersOrg::where("oid", $id)->get();
            $arrayUsers = [];
            foreach($qUsersOrg as $quo){
                $arrayUsers[ $quo->uid ] = $quo->id;
            }

            $arrayUsers = self::save_users($id, $usersCF, $arrayUsers);
            if(count($arrayUsers) > 0){
                $arrayUsersFix = [];
                foreach($arrayUsers as $u){
                    $arrayUsersFix[] = $u;
                }
                \Boot\Models\UsersOrg::whereIn("id", $arrayUsersFix)->delete();
            }

            //
        } else {
            // $result = static::get_direct_detail($director);
            // $organizations_data["director_detail"] = json_encode($result, JSON_UNESCAPED_UNICODE);
            // $organizations_data["director_detail"] = $result;
            $new_org = \Organizations\Model\Organizations::create($organizations_data);
            $id = $new_org->id;

            $saveBankArray = self::save_organizations_bank($id, $saveBank);

            self::save_users($id, $usersCF);

            $adminTpl->info($lang['org_add_success'], 'info', null, $lang['info'], $lang['org_list'], ADMIN . '/module/organizations');
        }
        echo '</div>';
		$adminTpl->admin_foot();
    }

    static function get_direct_detail($director){
        $dadata = new \Dadata();
        return $dadata->clean('name', $director);
    }

    static public function save_users($id, $usersCF, $arrayUsers = []){
        foreach($usersCF as $val){
            $json = json_decode($val, true);
            if(empty($json["data"]["id"])){

                $new_user = \Boot\Models\Users::create([
                    "nick" => $json["data"]["emailCF"],
                    "password" => md5(time()),
                    "tail" => gencode(rand(6, 11)),
                    "email" => $json["data"]["emailCF"],
                    "phone" => $json["data"]["phoneCF"],
                    "surname" => $json["data"]["surnameCF"],
                    "name" => $json["data"]["nameCF"],
                    "ochestvo" => $json["data"]["ochestvoCF"],
                    "group" => 2,
                    "regdate" => time(),
                    "active" => 1,
                    "hidden" => 0,
                    "phone2" => $json["data"]["phone2CF"],
                ]);

                $uid = $new_user->id;
            } else {	
                $uid = $json["data"]["id"];

                \Boot\Models\Users::find($uid)->update([
                    "email" => $json["data"]["emailCF"],
                    "phone" => $json["data"]["phoneCF"],
                    "surname" => $json["data"]["surnameCF"],
                    "name" => $json["data"]["nameCF"],
                    "ochestvo" => $json["data"]["ochestvoCF"],
                    "phone2" => $json["data"]["phone2CF"],
                ]);

                if(isset($arrayUsers[ $uid ])) unset($arrayUsers[ $uid ]);
                
            }

            $zz = \Boot\Models\UsersOrg::where("uid", $uid)->where("oid", $id);

            $zz_data = [
                "post" => $json["data"]["postCF"],
                "comment" => $json["data"]["commentCF"],
                "type" => $json["data"]["typeCF"],
            ];
            if($zz->count() > 0){
                $zz->update($zz_data);
            } else {
                $zz_data["uid"] = $uid;
                $zz_data["oid"] = $id;
                \Boot\Models\UsersOrg::create($zz_data);
            }
        }
        return $arrayUsers;
    }

    static public function save_organizations_bank($id, $saveBank){
        $saveBankArray = [];
        if(is_array($saveBank) && count($saveBank) > 0){
            foreach($saveBank as $val){
                $json = json_decode($val, true);
                
                if (empty($json["id"])) {
                    $json["oid"] = $id;
                    $org_new = \Organizations\Model\OrganizationsBank::create($json);
                    $saveBankArray[] = $org_new->id;
                } else {
                    \Organizations\Model\OrganizationsBank::find($json['id'])->update($json);
                    $saveBankArray[] = $json["id"];
                }
            }
        }
        
        return $saveBankArray;
    }

    static public function print($id){
        $org = \Organizations\Model\Organizations::find($id);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        $add_table = function($data) use ($section){
            $table = $section->addTable(["borderColor" => "000000", "borderSize" => 1]);
            $cellRowSpan = ['vMerge' => 'restart'];
            foreach($data as $row){
                $table->addRow();
                foreach($row as $cell){
                    $table->addCell($cell["width"] ?? null, $cellRowSpan)->addText($cell["text"]);
                }
            }
        };
        
        $section->addText("Организация {$org->name}", ["bold" => true], ["alignment" => "center"]);

        $width_title = 3000;
        $width_text = 8000;

        $section->addText("Основные данные:");
        $add_table([
            [
                ["text" => "ИНН", "width" => $width_title],
                ["text" => $org->inn, "width" => $width_text],
            ],
            [
                ["text" => "Краткое наименование", "width" => $width_title],
                ["text" => $org->name, "width" => $width_text],
            ],
            [
                ["text" => "Наименование", "width" => $width_title],
                ["text" => $org->name_full, "width" => $width_text],
            ],
            [
                ["text" => "Почтовый адрес", "width" => $width_title],
                ["text" => $org->maddress, "width" => $width_text],
            ],
            [
                ["text" => "КПП", "width" => $width_title],
                ["text" => $org->kpp, "width" => $width_text],
            ],
            [
                ["text" => "ОГРН", "width" => $width_title],
                ["text" => $org->ogrn, "width" => $width_text],
            ],
            [
                ["text" => "Должность руководителя", "width" => $width_title],
                ["text" => $org->post, "width" => $width_text],
            ],
            [
                ["text" => "Руководитель", "width" => $width_title],
                ["text" => $org->director, "width" => $width_text],
            ],
            [
                ["text" => "Действует на основании", "width" => $width_title],
                ["text" => $org->dey, "width" => $width_text],
            ],
        ]);

        $section->addText("\n");
        $section->addText("Контакты:");
        $add_table([
            [
                ["text" => "Телефон/Факс", "width" => $width_title],
                ["text" => $org->phone, "width" => $width_text],
            ],
            [
                ["text" => "E-mail", "width" => $width_title],
                ["text" => $org->email, "width" => $width_text],
            ],
        ]);

        $organizations_bank = $org->banks;
        if($organizations_bank->count() > 0){
            $section->addText("\n");
            $section->addText("Банковские данные:");
            
            foreach($organizations_bank as $item){
                $add_table([
                    [
                        ["text" => "Краткое наименование", "width" => $width_title],
                        ["text" => $item->name_short, "width" => $width_text],
                    ],
                    [
                        ["text" => "Наименование", "width" => $width_title],
                        ["text" => $item->name, "width" => $width_text],
                    ],
                    [
                        ["text" => "Адрес банка", "width" => $width_title],
                        ["text" => $item->address, "width" => $width_text],
                    ],
                    [
                        ["text" => "БИК банка", "width" => $width_title],
                        ["text" => $item->bik, "width" => $width_text],
                    ],
                    [
                        ["text" => "Корр. счет", "width" => $width_title],
                        ["text" => $item->kschet, "width" => $width_text],
                    ],
                    [
                        ["text" => "Расчетный счет", "width" => $width_title],
                        ["text" => $item->rschet, "width" => $width_text],
                    ],
                ]);
                $section->addText("\n");
            }
        }
        

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $filename = "organizations_{$id}.docx";
        $objWriter->save($filename);
        OfficeSupport::get_file($filename);
    }
}