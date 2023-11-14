<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2020 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */


namespace Api\App\Method;

class SupportMessage extends \Api\App\AppMethodLoader
{
	protected $model = \Support\Models\SupportMessage::class;
	protected $table = 'support_message';

	// public function get(){
	// 	$this->data = $this->model::where("uid", $this->uid);
	// 	return $this->get_();
	// }

    public function store(){
        if(empty($this->request["support_id"])) throw new \Exception("Field `support_id` is empty!");
        if(empty($this->request["message"])) throw new \Exception("Field `message` is empty!");

        $support = new $this->model;
        $support->support_id = $this->request["support_id"];
        $support->uid = $this->uid;
        $support->message = $this->request["message"];
        $support->save();

        return [
            "id" => $support->id,
        ];
    }

    public function store_file(){
        if(empty($this->request["support_id"])) throw new \Exception("Field `support_id` is empty!");
        if(!isset($this->files["picture"])) throw new \Exception("Empty picture!");

        $support_id = $this->request["support_id"];

        $attach = new \Boot\Models\Attach;
        $file = $this->files["picture"];
        $new_file_name = time()."_{$file["name"]}";
        $attach->name = $file["name"];
        $attach->url = "files/support/".$new_file_name;
        $attach->pid = $support_id;
        $attach->module = "support";
        rename($file["tmp_name"], ROOT.$attach->url);
        chmod(ROOT.$attach->url, 0755);
        $attach->save();

        $support = new $this->model;
        $support->support_id = $support_id;
        $support->uid = $this->uid;
        $support->attach_id = $attach->id;
        $support->save();

        return [
            "id" => $support->id,
        ];
    }
}
