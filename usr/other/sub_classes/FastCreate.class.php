<?php

class FastCreate{
    public $func_name_submit = "FastCreate";
    public $modal_id = "FastCreate";
    public $modal_title = "FastCreate";
    public $modal_body = "";
    public $ajax_url = "";
    public $ajax_success = "";
    public $result_name = "result";

    public function __construct($name = ""){
        if(!empty($name) && method_exists($this, $name)){
            $this->$name();
        }
    }

    static function get($name){
        return (new static($name))->get_fast_create();
    }

    public function get_fast_create(){
        return (object)[
            "modal" => $this->modal(),
            "button_open" => $this->button_open(),
            "js" => $this->js(),
            "script" => $this->script(),
        ]; 
    }

    public function object(){
        $this->modal_title = "Добавить объект";
        $this->modal_id = "modal_add_object";
        $this->func_name_submit = "modal_add_object";
        $this->modal_body = '<div class="section">
                                <label for="text" class="field prepend-icon">
                                    <input id="name" name="name" type="text" placeholder="Введите имя объекта" class="gui-input" />
                                    <label for="text" class="field-icon"><i class="fas fa-university"></i></label>
                                </label>
                            </div>
                            <div class="section">
                                <label for="text" class="field prepend-icon">
                                    <input id="address" name="address" type="text" placeholder="Введите адрес объекта" class="gui-input" />
                                    <label for="text" class="field-icon"><i class="fas fa-map-marker"></i></label>
                                </label>
                            </div>';
        $this->ajax_url = "/administration/module/object/add_ajax";
        $this->ajax_success = 'if ('.$this->result_name.' == "error"){
            notif("danger", "Информация", "Что-то пошло не так, возможно заполнены не все поля!");	
        } else {
            notif("success", "Информация", "Объект успешно добавлена!");	
        }';
    }

    public function organization(){
        $this->modal_title = "Добавить контрагента";
        $this->modal_id = "modal_add_organization";
        $this->func_name_submit = "modal_add_organization";
        $this->modal_body = '<div class="section">
        <label for="text" class="field prepend-icon">
            <input id="inn" name="inn" type="text" placeholder="Введите ИНН" class="gui-input" />
            <label for="text" class="field-icon"><i class="fas fa-money-check"></i></label>
        </label>
    </div>
    <div class="section">
        <label for="text" class="field prepend-icon">
            <input id="bik" name="bik" type="text" placeholder="БИК" class="gui-input" />
            <label for="text" class="field-icon"><i class="fa fa-bank"></i></label>
        </label>
    </div>
    <div class="section">
        <label for="text" class="field prepend-icon">
            <input id="rs" name="rs" type="text" placeholder="Расчетный счет" class="gui-input" />
            <label for="text" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
        </label>
    </div>
    <div class="section">
        <label for="text" class="field prepend-icon">
            <input id="phone" name="phone" type="text" placeholder="Контактный телефон" class="gui-input" />
            <label for="text" class="field-icon"><i class="fa fa-phone"></i></label>
        </label>
    </div>
    <div class="section">
        <label for="text" class="field prepend-icon">
            <input id="email" name="email" type="text" placeholder="Контактный E-mail" class="gui-input" />
            <label for="text" class="field-icon"><i class="fa fa-envelope"></i></label>
        </label>
    </div>';
        $this->ajax_url = "/administration/module/organizations/ajax/org_add";
        $this->ajax_success = '
        switch('.$this->result_name.'){
            case "1":
                notif("success", "Информация", "Организация успешно добавлена!");	
            break;
            case "2":
                notif("warning", "Информация", "Организация уже есть в базе данных!");	
            break;
            case "3":
                notif("danger", "Информация", "Что-то пошло не так, возможно заполнены не все поля!");
            break;
            case "4":
                notif("danger", "Информация", "Такая организация не существует");
            break;
        }';
    }

    public function button_open(){
        return 'data-toggle="modal" data-target="#'.$this->modal_id.'"';
    }

    public function modal(){
        return '<div class="modal fade admin-form" id="'.$this->modal_id.'" tabindex="-1" role="dialog" aria-labelledby="'.$this->modal_id.'_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
          <form action="javascript:void(null);" onsubmit="'.$this->func_name_submit.'();" id="'.$this->modal_id.'_form" method="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="'.$this->modal_id.'_label">'.$this->modal_title.'</h5>
            </div>
            <div class="modal-body">
                '.$this->modal_body.'
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Создать">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
            </form>
          </div>
        </div>
      </div>';
    }

    public function script(){
        return '<script>'.$this->js().'</script>';
    }

    public function js(){
        return 'function '.$this->func_name_submit.'() {
            $("#'.$this->modal_id.'").modal("hide");
            const form = $("#'.$this->modal_id.'_form");
            const data = form.serialize();
            form[0].reset();
            $.post("'.$this->ajax_url.'", data, function('.$this->result_name.'){
                '.$this->ajax_success.'
            });
		}';
    }
}