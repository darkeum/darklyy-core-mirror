<?php

namespace Reestr;

class ReestrModule{
	public $model_out = false;
    public $modal_name = "reestrModal";
	public $modal_form_name = "reestrModalForm";
	public $module_in = "reestr";
	public $modal_btn = "reestrModalBtn";
	public $local_script;
	public $oid_field = "oid";
	public $module_out;
	public $pid_out;

    public function init(){
		if(empty($this->model_out)) throw new \Exception("Empty field `model_out`");
		if(empty($this->module_out)) throw new \Exception("Empty field `module_out`");
		if(empty($this->pid_out)) throw new \Exception("Empty field `pid_out`");

        echo $this->modal();
        echo $this->script();
    }

    public function modal(){
        return <<<HTML
            <div class="modal fade admin-form" id="{$this->modal_name}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		      <div class="modal-dialog modal-dialog-centered" role="document">
		        <div class="modal-content">
		          <div class="modal-header">
		            <h5 class="modal-title">Договор</h5>
		          </div>
		          <div class="modal-body">
		            <form onsubmit="return false;" id="{$this->modal_form_name}">

					</form>
		          </div>
		          <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="{$this->modal_btn}" onclick="reestr_module.save();">Сохранить</button>
		            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Закрыть</button>
		          </div>
		        </div>
		      </div>
		    </div>
HTML;
    }

	public function script(){
		$model_out = str_replace("\\", "/", $this->model_out);
		return <<<HTML
			<script>
				const reestr_module = {
					modal: $('#{$this->modal_name}'),
					form: $('#{$this->modal_form_name}'),
					module_in: '{$this->module_in}',
					module_out: '{$this->module_out}',
					pid_out: '{$this->pid_out}',
					model_out: '{$model_out}',
					modal_btn: $("#{$this->modal_btn}"),
					oid_field: '{$this->oid_field}',
					async show(){
						this.modal.modal("show");
						this.form.html(`Загрузка договоров...`);
						this.modal_btn.prop("disabled", true);
						const pact = await this.get_pact();
						this.form.html(pact.template);
					},
					hide(){
						this.modal.modal("hide");
					},
					async get_pact(){
						const pact = await $.post('/administration/module/reestr/load_pact',
						{module_in: this.module_in, module_out: this.module_out, pid_out: this.pid_out, "model_out": this.model_out, "oid_field": this.oid_field})
						return JSON.parse(pact);
					},
					dis_off(){
						this.modal_btn.prop("disabled", false);
					},
					get_pid_in_pact(){
						return $("input[name=radio_reestr]:checked").val();
					},
					async save(){
						this.hide();
						// const lw = new LW;
						// lw.on();
						await this.delete_pact_relation();
						await this.add_pact_relation(this.get_pid_in_pact());
						await this.update_head();
						await this.after_save();
						// lw.off();
					},
					async after_save(){},
					async update_head(){},
					async add_pact_relation(pid_in){
						const formData = new FormData();
						formData.append("module_out", this.module_out);
						formData.append("pid_out", this.pid_out);
						formData.append("module_in", this.module_in);
						formData.append("pid_in", pid_in);
						return await ( await fetch("/administration/module/reestr/add_pact_relation", {
							method: 'POST',
							body: formData,
						}) ).json();
					},
					async delete_pact_relation(){
						const formData = new FormData();
						formData.append("module_out", this.module_out);
						formData.append("pid_out", this.pid_out);
						formData.append("module_in", this.module_in);
						return await ( await fetch("/administration/module/reestr/delete_pact_relation", {
							method: 'POST',
							body: formData,
						}) ).json();
					},
				};

				{$this->local_script}
			</script>
HTML;
	}

	static public function load_pact($request){
		if(empty($request["module_out"])) throw new \Exception("Empty field `module_out`");
		if(empty($request["pid_out"])) throw new \Exception("Empty field `pid_out`");
		if(empty($request["model_out"])) throw new \Exception("Empty field `model_out`");
		$oid_field = $request["oid_field"] ?? "oid";

		$model_out = str_replace("/", "\\", $request["model_out"]);
		$oid = $model_out::find($request["pid_out"])->$oid_field;

		return self::load_pact_data([
			"oid" => $oid,
			"module_out" => $request["module_out"],
			"pid_out" => $request["pid_out"],
		]);
	}

	static function pact_data($request){
		if(empty($request["oid"])) throw new \Exception("Empty field `oid`");
		if(empty($request["module_out"])) throw new \Exception("Empty field `module_out`");
		if(empty($request["pid_out"])) throw new \Exception("Empty field `pid_out`");
		$oid = $request["oid"];

		$r_data = \Reestr\Models\Reestr::where("oid", $oid)->get();

		$relations = new \Relations;
		// dd($request);
        $rf = $relations->find(["module_in" => "reestr", "module_out" => $request["module_out"], "pid_out" => $request["pid_out"]]);
        $rid = isset($rf[0]["pid_in"]) ? $rf[0]["pid_in"] : false;

		return [
			"reestr" => $r_data,
			"rid" => $rid,
		];
	}

	static public function load_pact_data($request){
		$data = static::pact_data($request);
		$r_data = $data["reestr"];
		$rid = $data["rid"];
        $count = $r_data->count();

		$onchange = $request["onchange"] ?? "reestr_module.dis_off();";
		$data = [
			"template" => "Договора не найдены!",
		];

        if($count > 0){
            $name = "radio_reestr";
			$template = "";

            foreach($r_data as $key => $r){
                $l_name = $name."_".$r->id;
				$checked = $rid == $r->id ? "checked" : "";

				$text = static::pact_text($r);

                $ul_start = '';
                $ul_end = '';
                if($key == 0) { $ul_start = '<ul class="list-group">'; }
                if($key == $count) { $ul_end = '</ul>'; }

				$template .= <<<HTML
                {$ul_start}
                    <li for="{$l_name}" class="list-group-item d-flex align-items-center">
                        <input {$checked}
                        onchange="{$onchange}"
                        type="radio"
                        value="{$r->id}"
                        id="{$l_name}"
                        class="form-check-input me-1"
                        name="{$name}">

                        <span class="cp">{$text}</span>

                        <a href="/administration/module/reestr/view/{$r->id}" class="cp ms-auto" target="_blank"><i class="fas fa-external-link-alt font-medium-2"></i></a>
                    </li>
                {$ul_end}
HTML;
            }
			$data["template"] = $template;
        }

		return $data;
	}

	static public function pact_template($pid_in){
		$dogovor_link = find_doc_in_reestr($pid_in, ["limit" => 1]);
		$dogovor_link_id = $pid_in;
		if(count($dogovor_link) > 0) $dogovor_link_id = $pid_in.'&doc_id='.$dogovor_link[0]["id"];
		$pact = \Reestr\Models\Reestr::with("reestr_type")->where("id", $pid_in)->first();
		return '<a target="_blank" href="/administration/module/reestr/view/'.$dogovor_link_id.'">'.static::pact_text($pact).'</a>';
	}

	static public function pact_template_new($pid_in){
		$dogovor_link = find_doc_in_reestr($pid_in, ["limit" => 1]);
		$dogovor_link_id = $pid_in;
		$pact = \Reestr\Models\Reestr::with("reestr_type")->where("id", $pid_in)->first();
		return '<a target="_blank" href="/administration/module/reestr/'.$dogovor_link_id.'">'.static::pact_text($pact).'</a>';
	}

	static function pact_text($pact){
		return "№{$pact->reestr_type->short} - {$pact->numb} от ".date("d.m.Y", $pact->date);
	}
}