<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\System\Core\Abstracts\Controllers;

use Boot\Support\Facades\Request;

abstract class WebController extends Controller
{
   /**
     * Тип этого контроллера. Это будет доступно зеркально в Действиях.
     * Предоставление каждому действию возможности изменять свою внутреннюю бизнес-логику в зависимости от типа пользовательского интерфейса, вызвавшего его.
     */
    public string $ui = 'web';

    public static function grid()
    {
        if (Request::has('patch')) {
            $gridClassName = '\\' . Request::get('patch');
            if (class_exists($gridClassName)) {
                $grid = new $gridClassName;
                $out = $grid->autorun();
                if(is_array($out)){
                    return $out;
                } else {
                    echo $out;
                }
                
            }
        }
    }
}
