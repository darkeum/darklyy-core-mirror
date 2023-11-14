<?php

use Boot\System\Office\OfficeSupport;

abstract class FTP{
    protected $host;
    protected $username;
    protected $password;
    protected $conn_id;
    protected $mod = FTP_BINARY;
    public $local_file;
    public $remote_file;

    public function __construct(){
        $this->conn_id = ftp_connect($this->host) or die("Не удалось установить соединение с ".$this->host); ;
        if (!ftp_login($this->conn_id, $this->username, $this->password)) exit("Не могу соединиться");
    }

    public function __destruct(){
        ftp_close($this->conn_id);
    }

    public function ftp_rawlist($directory, $recursive = false){
        return ftp_rawlist($this->conn_id, $directory, $recursive);
    }

    public function ftp_size($filename){
        return ftp_size($this->conn_id, $filename); 
    }

    public function ftp_nlist($directory){
        return ftp_nlist($this->conn_id, $directory);
    }

    public function ftp_mlsd($directory){
        return ftp_mlsd($this->conn_id, $directory);
    }

    public function ftp_pwd(){
        return ftp_pwd($this->conn_id);
    }

    public function get(){
        //local_file, remote_file
        $ext = pathinfo($this->local_file);
        if(!file_exists($ext["dirname"])){
            mkdir($ext["dirname"], 0777, true);
        }
        ftp_get($this->conn_id, $this->local_file, $this->remote_file, $this->mod);
    }

    public function put(){
        //remote_file, local_file
        if(file_exists($this->local_file)){
            $this->ftp_mksubdirs();
            return ftp_put($this->conn_id, $this->remote_file, $this->local_file, $this->mod);
        }
        return false;
    }

    public function delete(){
        //remote_file
        if(!empty($this->remote_file)){
            ftp_delete($this->conn_id, $this->remote_file);
        }
    }

    public function get_file(){
        //local_file, remote_file
        $this->get();
        static::get_local_file($this->local_file);
    }

    static function get_local_file($file){
        OfficeSupport::get_file($file);
    }

    public function put_file(){
        if($this->put()){
            @unlink($this->local_file);
        }
    }

    private function ftp_mksubdirs(){
        @ftp_chdir($this->conn_id, "/");
        $ext = pathinfo($this->remote_file);
        $parts = explode('/', $ext["dirname"]);
        foreach($parts as $part){
           if((!empty($part) || $part == 0 || $part == "0") && !@ftp_chdir($this->conn_id, $part)){
              ftp_mkdir($this->conn_id, $part);
              ftp_chdir($this->conn_id, $part);
              //ftp_chmod($this->conn_id, 0777, $part);
           }
        }
        ftp_chdir($this->conn_id, "/");
    }

    public function read_file(){
        ftp_pasv($this->conn_id, true);

        $h = fopen('php://temp', 'r+');

        ftp_fget($this->conn_id, $h, $this->remote_file, FTP_BINARY, 0);

        $fstats = fstat($h);
        fseek($h, 0);
        $contents = fread($h, $fstats['size']); 

        fclose($h);

        return $contents;
    }

    public function view_read_file(){
        $contents = $this->read_file();
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . ($this->filename ?? basename($this->remote_file)). '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ');
        header('Accept-Ranges: bytes');

        echo $contents;
    }

    public function get_mlsd_recursive($directory, $data = []){
        $list = $this->ftp_nlist($directory);
        foreach($list as $l){
            if($this->ftp_size($l) == -1){
                $data = $this->get_mlsd_recursive($l, $data);
            } else {
                $data[] = $l;
            }
        }
        return $data;
    }

    public function find_file($directory, $filename){
        $list = $this->ftp_nlist($directory);
        foreach($list as $l){
            $p = pathinfo($l);
            if($p["basename"] == $filename) return $l;
            if($this->ftp_size($l) == -1){
                $result = $this->find_file($l, $filename);
                if($result) return $result;
            } 
        }
        return false;
    }
}