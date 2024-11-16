<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Routing;

interface ResponseFactory
{
    /**
     * Создание нового экземпляра ответа.
     *
     * @param  array|string  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Boot\System\Http\Response
     */
    public function make($content = '', $status = 200, array $headers = []);

    /**
     * Создание нового ответа «без содержания».
     *
     * @param  int  $status
     * @param  array  $headers
     * @return \Boot\System\Http\Response
     */
    public function noContent($status = 204, array $headers = []);

    /**
     * Создание нового ответа для данного представления.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Boot\System\Http\Response
     */
    public function view($view, $data = [], $status = 200, array $headers = []);

    /**
     * Создание нового экземпляра ответа JSON.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \Boot\System\Http\JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [], $options = 0);

    /**
     * Создание нового экземпляра ответа JSONP.
     *
     * @param  string  $callback
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \Boot\System\Http\JsonResponse
     */
    public function jsonp($callback, $data = [], $status = 200, array $headers = [], $options = 0);

    /**
     * Создание нового экземпляра потокового ответа.
     *
     * @param  callable  $callback
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function stream($callback, $status = 200, array $headers = []);

    /**
     * Создание нового экземпляра потокового ответа в виде загрузки файла.
     *
     * @param  callable  $callback
     * @param  string|null  $name
     * @param  array  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamDownload($callback, $name = null, array $headers = [], $disposition = 'attachment');

    /**
     * Создание нового ответа на загрузку файла.
     *
     * @param  \SplFileInfo|string  $file
     * @param  string|null  $name
     * @param  array  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($file, $name = null, array $headers = [], $disposition = 'attachment');

    /**
     * Возвращает необработанное содержимое двоичного файла.
     *
     * @param  \SplFileInfo|string  $file
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function file($file, array $headers = []);

    /**
     * Создание нового ответа с перенаправлением по указанному пути.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirectTo($path, $status = 302, $headers = [], $secure = null);

    /**
     * Создание нового ответа с перенаправлением на именованный маршрут.
     *
     * @param  string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirectToRoute($route, $parameters = [], $status = 302, $headers = []);

    /**
     * Создание нового ответа с перенаправлением на действие контроллера.
     *
     * @param  array|string  $action
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirectToAction($action, $parameters = [], $status = 302, $headers = []);

    /**
     * Создание нового ответа с перенаправлением, поместив текущий URL в сеанс.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirectGuest($path, $status = 302, $headers = [], $secure = null);

    /**
     * Создание нового ответа с перенаправлением на ранее запланированное место.
     *
     * @param  string  $default
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirectToIntended($default = '/', $status = 302, $headers = [], $secure = null);
}
