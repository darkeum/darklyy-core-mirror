<?php

use Boot\Support\Facades\Auth;

if (!function_exists('langConst')) {
    function langConst($key)
    {
        if (defined($key)) {
            echo constant($key);
        }
    }
}

if (!function_exists('menu_open')) {
    function menu_open($matches)
    {

        $url = explode('/', \Boot\Support\Facades\Request::path());
        $type = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if ($url[1] == 'module') {
                $ccount = 2;
            }
            if (in_array($url[$ccount], $typeArray)) {
                return 'menu-open';
            }
        }
    }
}

if (!function_exists('menu_choose')) {
    function menu_choose($matches)
    {
        $url = explode('/', \Boot\Support\Facades\Request::path());
        $type = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if ($url[1] == 'module') {
                $ccount = 2;
            }
        } else {
            $ccount = 0;
        }
        if ((in_array($url[$ccount], $typeArray)) || (($type == 'main') && (empty($url[1])))) {
            return 'class="active"';
        }
    }
}

if (!function_exists('menu_choose_module')) {
    function menu_choose_module($matches)
    {
        $url = explode('/', \Boot\Support\Facades\Request::path());
        $type = $matches[2];
        $module = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if (($url[1] == 'module') && ($url[2] == $module) && (isset($url[3])) && in_array($url[3], $typeArray)) {
                return 'class="active"';
            }
            if (($url[1] == 'module') && ($url[2] == $module) && ($typeArray[0] == 'main') && (!isset($url[3]))) {
                return 'class="active"';
            }
        }
    }
}

if (!function_exists('checkActiveBlade')) {

    function checkActiveBlade($matches)
    {
        global $core;
        $content = $matches[1];

        $url = explode('/', \Boot\Support\Facades\Request::path());
        if (isset($url[1])) {
            if ($url[1] == 'update') {
                return false;
            }
            if (isset($url[2])) {
                if ($url[2] == 'changelog') {
                    return false;
                }
            }
            if ($url[1] == 'module') {
                if ($core->checkModule($url[2]) == '1') {
                    return stripslashes($content);
                }
            } else {
                return stripslashes($content);
            }
        } else {

            return '<header id="topbar" class="alt" style="padding: 30px 20px">
      <div class="topbar-left pull-left">
        <h1 class="mn" >Корпоративный портал</h1><span class="text-shady-lady fs15">version ' . VERSION_ID . '</span>
      </div> 
<div class="topbar-right">
                        <a class="btn btn-system" href="' . ADMIN . '/calendar"><span class="mr10 icon fal fa-calendar"></span>Календарь событий</a> 
                        <a class="btn btn-system" href="javascript:void(0);" onclick="editDashboard();"><span class="icon fal fa-cog"></span></a>
                      </div>		  
    </header>';
        }
    }
}


