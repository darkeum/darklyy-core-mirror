<?php

abstract class HBF{

    protected $id;
    protected $title_page = "";
    protected $title = "";
    protected $route_start = 4;

    protected $update_function_name = "update_body();";
    protected $body_form_id = "bodyForm";
    protected $tbody_id = "table_tbody";

    protected $url;

    protected $query;
    protected $query_;
    protected $fields;
    protected $fields_;
    protected $db_fields;

    protected $victoria = "\Victoria";
    protected $victoria_;

    protected $model = false;
    protected $model_detail = false;

    protected $version;

    protected $script = false;

    abstract public function head();

    abstract function init();

    public function __construct(){
        $this->version = time();
    }

    public function url_generate(){
        global $url;
        $u = array_slice($url, 0, $this->route_start);
        $u = "/".implode("/", $u);
        $this->url->victoria_add     = $u."/victoria_add/".$this->id;
        $this->url->victoria_add_min = $u."/victoria_add_min/\${id}";
        $this->url->add              = $u."/add/".$this->id;
        $this->url->update           = $u."/update/".$this->id;
        $this->url->update_detail    = $u."/update_detail/";
        $this->url->delete           = $u."/delete/";
        $this->url->update_body      = $u."/update_body/".$this->id;
    }

    public function init_id($id){
        $this->id = $id;
        $this->url = (object)[];
        $this->init();
        $this->prepare_fields();
        $this->prepare_query();
        $this->url_generate();
        if(class_exists($this->victoria)) $this->victoria_ = new $this->victoria;
    }

    public function route(){
        global $url;
        $other_id = isset($url[ $this->route_start + 1 ]) ? $url[ $this->route_start + 1 ] : false;
        switch(isset($url[ $this->route_start ]) ? $url[ $this->route_start ] : '') {
            default:
                $this->init_id($url[ $this->route_start ]);
                $this->view();
            break;
            case "add":
                $this->init_id($other_id);
                $this->add($_POST);
            break;
            case "update":
                $this->init_id($other_id);
                $this->update($this->id, $_POST);
            break;
            case "update_detail":
                $this->init_id($other_id);
                $this->update_detail($this->id, $_POST);
            break;
            case "delete":
                $this->init_id($other_id);
                $this->delete($this->id);
            break;
            case "victoria_add":
                $this->init_id($other_id);
                $this->victoria_->id = $this->id;
                $this->victoria_->url_add = $this->url->add;
                $this->victoria_->local_script = '$victoria.transfer_to_document_update = opener.update_body;';
                echo $this->victoria_->popup();
            break;
            case "victoria_add_min":
                $this->init_id($other_id);
		        $this->victoria_->transfer_to_document_disabled = true;
		        $this->victoria_->id = $this->id;
		        $this->victoria_->local_script = <<<HTML
		        	\$victoria.add_basket = async function(cid){
		        		const id = this.id;
                        if(typeof (opener.replace_item) === "function"){
                            await opener.replace_item(id, cid);
		        		    await this.add_basket_func(id);
		        		    window.close();
                        } else {
                            Swal.fire(
                                "Error!",
                                "Function `replace_item` not found!",
                                "error"
                            );
                        }
		        	}
HTML;
		        echo $this->victoria_->popup();
            break;
            case "update_body":
                $this->init_id($other_id);
                echo $this->body_tbody();
            break;
        }
    }

    public function prepare_fields(){
        $default_fields = ["label" => "", "db" => false, "hidden" => false, "type" => "text", "value" => function(){},];
        // dd($default_fields);
        $fields = $this->fields;
        $new_fields = [];
        $db_fields = [];
        foreach($fields as $key => $item){
            $new_item = [];
            foreach($default_fields as $k => $i){
                $new_item[ $k ] = isset($item[ $k ]) ? $item[ $k ] : $i;
            }
            if($new_item["db"]){
                $db_name = $new_item["db"];
                $db_fields[] = $db_name." as ".$this->prepare_db_name($db_name);
            } 
            $new_item["key"] = $key;
            $new_fields[ $key ] = $new_item;
        }
        $this->fields_ = $new_fields;
        $this->db_fields = implode(", ", $db_fields);
    }

    public function prepare_db_name($db_name){
        $dne = explode(".", $db_name);
        return $dne[0]."_".$dne[1];
    }

    public function prepare_query(){
        $this->prepare_where();
        $query = $this->query;
        $query = str_replace("{fields}", $this->db_fields, $query);
        $query = str_replace("{where}", $this->where, $query);
        $this->query_ = $query;
    }

    public function prepare_where(){
        $this->where = "";
    }

    public function body(){
        $data = [];
        $data[] = '<table class="table table-striped">';
            $data[] = '<thead id="table_thead"><tr>';
                $data[] = $this->body_thead();
            $data[] = '</tr></thead>';
            $data[] = '<tbody id="table_tbody">';
                $data[] = $this->body_tbody();
            $data[] = '</tbody>';
            $data[] = '<tfoot class="footer-menu">';
                $data[] = $this->body_tfoot();
            $data[] = '</tfoot>';
        $data[] = "</table>";
        return implode("", $data);
    }

    public function body_thead(){
        $data = [];
        $fields = $this->fields_;
        foreach($fields as $field){
            $this->field = $field;
            if(!$this->field["hidden"]){
                $data[] = $this->body_thead_field();
            }
        }
        return implode("", $data);
    }

    public function body_thead_field(){
        $style = [];
        $style = count($style) > 0 ? "style=\"".implode(" ", $style)."\"" : "";
        switch($this->field["type"]){
            default:
                $value = $this->field["label"];
            break;
            case "checkbox":
                $value = <<<HTML
                    <div class="checkbox-custom mb15">
				    	<input id="all" type="checkbox" name="all" onclick="setCheckboxes('{$this->body_form_id}', true); return true;" class="checks">
				    	<label for="all"></label>
				    </div>	
HTML;
            break;
        }
        return <<<HTML
            <th {$style}>{$value}</th>
HTML;
    }

    public function body_tbody(){
        global $db;
        $data = [];
        $fields = $this->fields_;
        $this->counter = 0;
        // dd($this->fields_);
        $query = $db->query($this->query_);
        if($db->numRows($query) > 0){
            while($item = $db->getRow($query)){
                $this->item = (object) $item;
                $this->counter++;
                $data[] = "<tr>";
                foreach($fields as $field){
                    $this->field = $field;
                    if(!$this->field["hidden"]){
                        $data[] = $this->body_tbody_field();
                    }
                }
                $data[] = "</tr>";
            }
        }
        return implode("", $data);
    }

    public function get_field($field){
        if(isset($this->fields_[ $field ])){
            return $this->prepare_db_name($this->fields_[ $field ]["db"]);
        } else {
            throw new Exception("Field `{$field}` is empty!");
        }
    }

    public function get_id_field(){
        return $this->get_field("id");
    }

    public function get_id_value(){
        $field = $this->get_id_field();
        return $this->item->$field;
    }

    public function get_cid_field(){
        return $this->get_field("cid");
    }

    public function get_cid_value(){
        $field = $this->get_cid_field();
        return $this->item->$field;
    }

    public function get_stock_id_field(){
        return $this->get_field("stock_id");
    }

    public function get_stock_id_value(){
        $field = $this->get_stock_id_field();
        return $this->item->$field;
    }

    public function body_tbody_field(){
        $type = $this->field["type"];
        $func_name = "type_{$type}";
        if(method_exists($this, $func_name)){
            $data = $this->$func_name();

            if(!is_array($data)) throw new Exception("Not an array passed! Type: `{$type}`; result : `{$data}`");

            $value      = isset($data["value"]) ? $data["value"] : "";
            $style      = isset($data["style"]) ? $data["style"] : "";
            $class      = isset($data["class"]) ? $data["class"] : "";
            // $class      = $this->field["key"]." ".$class;
            $onclick    = isset($data["onclick"]) ? $data["onclick"] : "";
            $ondblclick = isset($data["ondblclick"]) ? $data["ondblclick"] : "";
            $ndr        = isset($data["ndr"]) ? $data["ndr"] : "";
            
            $class      = empty($class) ? "" : "class=\"{$class}\"";
            $style      = empty($style) ? "" : "style=\"{$style}\"";
            $onclick    = empty($onclick) ? "" : "onclick=\"{$onclick}\"";
            $ondblclick = empty($ondblclick) ? "" : "ondblclick=\"{$ondblclick}\"";
            return <<<HTML
                <td {$style} {$class} {$ndr} {$onclick} {$ondblclick}>{$value}</td>
HTML;
        } else {
            throw new Exception("Method `{$func_name}` not found!");
        }
    }

    public function type_text(){
        $result = [];
        if($this->field["db"]){
            $f = $this->prepare_db_name($this->field["db"]);
            $result["value"] = $this->item->$f;
        }
        return $result;
    }

    public function type_name(){
        $result = [];
        $data = $this->type_text();
        $id = $this->get_id_value();
        $result["value"] = ee($data["value"]);
        $result["class"] = "cp";
        $result["ondblclick"] = "change_item('{$id}')";
        return $result;
    }

    public function type_counter(){
        $result = [];
        $result["value"] = $this->counter;
        $result["class"] = "position";
        return $result;
    }

    public function type_value(){
        $result = [];
        $result["value"] = $this->field["value"]();
        return $result;
    }

    public function type_checkbox(){
        $result = [];
        $id = $this->get_id_value();
        $result["value"] = <<<HTML
            <div class="checkbox-custom mb15">
                <input id="checkbox{$id}" type="checkbox" name="checks[]" value="{$id}" class="checks">
                <label for="checkbox{$id}"></label>
            </div>
HTML;
        return $result;
    }

    public function type_ndr(){
        $result = [];
        $id = $this->get_id_value();
        $cid = $this->get_cid_value();
        $stock_id = $this->get_stock_id_value();
        $result["value"] = get_ndr(["cid" => $cid, "stock_id" => $stock_id, "module" => "sales", "pid" => $id, "returnType" => "view"]);
        $result["class"] = "ndr cp";
        $result["onclick"] = "view_ndr_modal('{$cid}');";
        $result["ndr"] = <<<HTML
            data-cid="{$cid}"
HTML;
        return $result;
    }

    public function body_tfoot(){
        return <<<HTML
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act"></select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="Выполнить" disabled onclick="subAct();"><span class="pd-l-sm"></span>	
						 </nav>
                      </td>					 
                    </tr>
                </tfoot> 
HTML;
    }

    public function foot(){}

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head($this->title_page);
        echo <<<HTML
            <link rel="stylesheet" type="text/css" href="/usr/other/sub_classes/hbf/assets/css/style.css?{$this->version}">
            <section id="content">
                <h3 class="text-center">{$this->title}</h3>
                <div class="row" id="admin-form">
                    <div class="panel mb25">
                        <div class="panel-body p20 pb10">
                            <div class="section row mbn admin-form">
                                <div class="col-md-12 pl15">
                                    <div class="section row mb15" id="head">
                                        {$this->head()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
	        		<div class="panel panel-dark panel-border top">
	        			<div class="panel-heading">
	        				<span class="panel-title">Список товаров:</span>
	        				<div class="widget-menu pull-right" >
                                <div class="btn-group">
                                    {$this->btn()}
                                </div>				
	        				</div>	
                        </div>

                        <div class="panel-body pn table-responsive"> 
	        			    <form id="{$this->body_form_id}" onsubmit="return false;">
                                <div id="body">{$this->body()}</div>
                                <div id="foot">{$this->foot()}</div>
	        			    </form>
	        		    </div>			
                    </div>
                </div>
	        </section>
HTML;
        echo $this->modal();
        $ndr = new \NDR;
        $ndr->init();
	    $this->victoria_->link = $this->url->victoria_add_min;
	    $vm = $this->victoria_->open_func();
        $adminTpl->footIncludes[] = <<<HTML
            <script>
                $(function(){
                    $("#{$this->tbody_id}").sortable({
                      update: function( event, ui ) {
                        const position = $(".position");
                        position.each(function(index) {
	                	      $(this).html(index + 1);
                        });
                      },
                      axis: "y",
                      handle: ".position"
                    });
                });
                
                async function update_body(){
                    const data = await $.post("{$this->url->update_body}");
                    $("#{$this->tbody_id}").html(data);
                } 

                async function delete_item(id){
                    await $.post(`{$this->url->delete}/\${id}`);
                }

                function change_item(id){
                    {$vm}
                }
            </script>
HTML;
        $adminTpl->footIncludes[] = "<script src=\"/usr/other/sub_classes/hbf/assets/js/script.js?{$this->version}\"></script>";

        if(is_array($this->script)){
            foreach($this->script as $script){
                $adminTpl->footIncludes[] = $script;
            }
        } else if(!empty($this->script)) {
            $adminTpl->footIncludes[] = $this->script;
        }
        
        $adminTpl->admin_foot();
    }

    public function btn(){
        $this->victoria_->link = $this->url->victoria_add;
		$victoria_btn = $this->victoria_->button();
        return <<<HTML
            {$victoria_btn}
            <button onclick="{$this->update_function_name} notif('primary', 'Информация', 'Данные успешно обновлены!');" type="button" class="btn btn-xs btn-system">
                <span class="mr10 icon fa fa-refresh"></span>Обновить
            </button>
HTML;
    }

    public function modal(){
        
    }

    public function add($request){
        
    }

    public function update($id, $request){
        if($this->model){
            $this->model->update($id, $request);
        }
    }

    public function update_detail($id, $request){
        if($this->model_detail){
            $this->model_detail->update($id, $request);
        } 
    }

    public function delete($id){
        if($this->model_detail){
            $this->model_detail->delete($id);
        } 
    }

    public function btn_delete(){
        $id = $this->get_id_value();
        return <<<HTML
            <button type="button" class="btn btn-xs btn-danger" title="Удалить" onclick="m_delete({$id});"><i class="fas fa-times"></i></button>
HTML;
    }
}