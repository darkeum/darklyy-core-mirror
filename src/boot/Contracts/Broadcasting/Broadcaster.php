<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Broadcasting;

interface Broadcaster
{
    /**
     * Аутентификация входящего запроса для данного канала.
     *
     * @param  \Boot\System\Http\Request  $request
     * @return mixed
     */
    public function auth($request);

    /**
     * Верните действительный ответ аутентификации.
     *
     * @param  \Boot\System\Http\Request  $request
     * @param  mixed  $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result);

    /**
     * Транслировать данное событие.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     *
     * @throws \Boot\System\Broadcasting\BroadcastException
     */
    public function broadcast(array $channels, $event, array $payload = []);
}
