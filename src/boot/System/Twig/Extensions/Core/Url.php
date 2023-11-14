<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Twig\Extensions\Core;

use Boot\System\Routing\UrlGenerator;
use Boot\Support\Str;
use Boot\System\Routing\Router;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;


class Url extends AbstractExtension
{
    /**
     * Экземпляр UrlGenerator
     * 
     * @var \Boot\System\Routing\UrlGenerator
     */
    protected $url;

    /**
     * Экземпляр Router
     * 
     * @var \Boot\System\Routing\Router
     */
    protected $router;

    /**
     * Создание расширения для работы с URL
     *
     * @param \Boot\System\Routing\UrlGenerator
     * @param \Boot\System\Routing\Router
     */
    public function __construct(UrlGenerator $url, Router $router)
    {
        $this->url = $url;
        $this->router = $router;
    }


    /**
     * Имя расширения
     *
     * @return void
     */
    public function getName()
    {
        return 'Dark_Extension_Core_Url';
    }


    /**
     * Функции расширения
     *
     * @return void
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('asset', [$this->url, 'asset'], ['is_safe' => ['html']]),
            new TwigFunction('action', [$this->url, 'action'], ['is_safe' => ['html']]),
            new TwigFunction('url', [$this, 'url'], ['is_safe' => ['html']]),
            new TwigFunction('route', [$this->url, 'route'], ['is_safe' => ['html']]),
            new TwigFunction('route_has', [$this->router, 'has'], ['is_safe' => ['html']]),
            new TwigFunction('secure_url', [$this->url, 'secure'], ['is_safe' => ['html']]),
            new TwigFunction('secure_asset', [$this->url, 'secureAsset'], ['is_safe' => ['html']]),
            new TwigFunction(
                'url_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = Str::camel($name);

                    return call_user_func_array([$this->url, $name], $arguments);
                }
            ),
        ];
    }

    /**
     * Создайте абсолютный URL-адрес для заданного пути.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function url($path = null, $parameters = [], $secure = null)
    {
        if (!$path) {
            return $this->url;
        }

        return $this->url->to($path, $parameters, $secure);
    }
}
