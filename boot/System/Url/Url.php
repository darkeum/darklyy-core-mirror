<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Url class
*/

namespace Boot\System\Url;

use \Boot\support\Facades\Request;

class Url
{

    protected $request;

    public function __construct()
    {
        $this->request = new Request;
    }

    /**
     * Получить текущий URL без строки запроса
     *
     * @return string Метод возвращает текущий URL без строки запроса
     */
    public function current()
    {
        return $this->request->getSchemeAndHttpHost() . $this->request->getBaseUrl() . $this->request->getPathInfo();
    }

    /**
     * Метод получает коллекцию путей из текущего URL
     *
     * @return \Boot\Support\Collection  Возвращает коллекцию путей из текущего URL
     */
    public function get()
    {
        $urlString = Request::get('url');
        $urlArray = explode('/',  $urlString);
        array_shift($urlArray);
        $url = cc($urlArray);
        $url = $url->filter(function ($value, $key) {
            return  $value != null;
        });
        return $url;
    }    

    /**
     * Получить текущий URL, включая строку запроса
     *
     * @return string Метод возвращает текущий URL, включая строку запроса
     */
    public function full()
    {
        return Request::getUri();
    }

    /**
     * Получаем полный URL для предыдущего запроса
     *
     * @return string Метод возвращает полный URL для предыдущего запроса
     */
    public function previous()
    {
        return $this->request->headers->get('previous');
    }
}
