<?php

namespace Boot\Traits;

/**
 * 
 */
trait Key{
    static public function key_create(){
        $fp = fopen(static::$key, "w");
        fclose($fp);
    }

    static public function key_delete(){
        if(static::key_check())
        unlink(static::$key);
    }

    static public function key_check(){
        return file_exists(static::$key);
    }
}
