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

class Support extends \Api\App\AppMethodLoader
{
	protected $model = \Support\Models\Support::class;
	protected $table = 'support';

	public function index(){
		$this->data = $this->model::where("uid", $this->uid);
		return $this->get_();
	}

    public function store(){
        if(empty($this->request["title"])) throw new \Exception("Field `title` is empty!");

        $support = new $this->model;
        $support->title = $this->request["title"];
        $support->uid = $this->uid;
        $support->save();

        return [
            "text" => "Заявка создана",
            "id" => $support->id,
        ];
    }

    public function show(){
        if(empty($this->request["id"])) throw new \Exception("Field `id` is empty!");

        $support = $this->model::findOrFail($this->request["id"]);
        return [
            "support" => $support,
            "messages" => $this->prepare_messages($support->messages()->get()),
        ];
    }

    public function show_support(){
        if(empty($this->request["id"])) throw new \Exception("Field `id` is empty!");

        return [
            "support" => $this->model::findOrFail($this->request["id"]),
        ];
    }

    public function show_messages(){
        if(empty($this->request["id"])) throw new \Exception("Field `id` is empty!");

        $messages = $this->model::findOrFail($this->request["id"])->messages;

        return [
            "messages" => $this->prepare_messages($messages),
        ];
    }

    private function prepare_messages($messages){
        foreach($messages as $message){
            $attach = $message->attach()->first();
            if($attach){
                $message->file = [
                    "id" => $attach->id,
                    "name" => $attach->name,
                    "url" => "https://atri-energo.ru/".$attach->url,
                ];
            } else {
                $message->file = null;
            }
        }
        return $messages;
    }
}
