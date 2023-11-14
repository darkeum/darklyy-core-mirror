<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Office;

class OfficeSupport
{
    static public function get_file($file)
    {
        if ($file && file_exists($file)) {
            $path_data = pathinfo($file);
            $size = filesize($file);
            header("Content-Disposition: attachment; filename*=UTF-8''".rawurlencode($path_data['basename']).";");
            header("Content-Length: $size");
            header("Charset: UTF-8");
            header("access-control-allow-origin: *");
            header("Content-Type: application/pdf");
            if (readfile($file)) {
                unlink($file);
            }
        } else {
            abort('404');
        }
    }
}
