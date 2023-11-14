<?php

use \Boot\Models\Attach;

class StorageFTP extends FTP{
    protected $host = "87.249.246.161";
    protected $username = "storage";
    protected $password = "M49a&1KJ2&";

    public function init_file_id($id){
        $data = Attach::findOrFail($id);
        $this->remote_file =  (env('APP_NAME') != 'AE' ? env('APP_NAME').'/' : '').$data->url;
        $path_data = pathinfo($data->url);
        $this->local_file = "{$path_data["dirname"]}/{$data->name}";
        $this->filename = $data->name;
    }

    public function get_file_id($id){
        $this->init_file_id($id);
        $this->get_file();
    }

    public function view_file_id($id){
        $this->init_file_id($id);
        $this->view_read_file();
    }

    public function route_file_id($id){
        $this->init_file_id($id);
        $ext = pathinfo($this->filename);
        switch(mb_strtolower($ext["extension"])){
            default:
                $this->get_file();
            break;
            case "pdf":
                $this->view_read_file();
            break;
        }
    }

    public function put_file_attach($request){
        //["file" => ["tmp_name" => "", "name" => ""], "id" => "", "module" => "", "mod" => ""]
        $file_source = $request["file"];
        $id = $request["id"];
        $comm_id = empty($request["comm_id"]) ? $id : $request["comm_id"];
        $module = $request["module"];
        $mod = $request["mod"];

        if(is_array($file_source)){
            $file = $file_source;
        } else {
            $file = [];
            $ext = pathinfo($file_source);
            $file["name"] = $ext["basename"];
            $file["tmp_name"] = $ext["dirname"];
        }

        $patch =  "files/".$module."/";
        if(!empty($file["tmp_name"]) && file_exists($file["tmp_name"])){
    
            // $ext = pathinfo($file["name"]);
            $full_name = time().'_'.$file["name"];
            $full_name = str_replace(" ", "", $full_name);
            $filePatch = $patch.$id."/".$full_name;
            // copy($file["tmp_name"], $filePatch);
    
            $data = [
                "name" => $file["name"],
                "url" => $filePatch,
                "pid" => $comm_id,
                "module" => $mod,
                "class_name" => static::class,
                "daemon" => 1,
            ];
            $attach = Attach::create($data);
            $iid = $attach->id;

            $this->local_file = $file["tmp_name"];
            $this->remote_file = $filePatch;
            $this->put_file();

            return $iid;
        }
    }
}