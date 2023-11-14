<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Notifications;

class Action
{
    /**
     * Текст действия
     *
     * @var string
     */
    public $text;

    /**
     * URL действия.
     *
     * @var string
     */
    public $url;

    /**
     * Создание нового экземпляра действия
     *
     * @param  string  $text
     * @param  string  $url
     * @return void
     */
    public function __construct($text, $url)
    {
        $this->url = $url;
        $this->text = $text;
    }
}
