<?php

namespace Project;

use \Project\Model\Blocks;

class BlocksFTP extends \FTP{
    protected $host = "94.198.55.98";
    protected $username = "ftp_user";
    protected $password = "zVZqW6McjX93Pe9Q";

    static function get_block_name($model, $data){
        $path = $data["path"];
        $view = $data["view"];
        $type = Blocks::getType();
        $links = Blocks::getViewLinks();
        $view_ = "";
        $p = pathinfo($path);
        if($view != "3d"){
            $view_ = "-{$links[ $view ][0]}";
        }
        return "{$type[ $model->type ]} {$model->code}{$view_}.{$p["extension"]}";
    }
    
    public function init_local_file($model, $data){
        $this->local_file = ROOT."files/blocks/{$model->id}/".static::get_block_name($model, $data);
    }
}