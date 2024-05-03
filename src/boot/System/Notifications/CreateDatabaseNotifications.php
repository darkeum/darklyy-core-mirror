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

trait CreateDatabaseNotifications
{

    public $database_notification = true;

    public $url = null;
    public $text = null;
    public $title = null;
    public $icon = null;
    public $module = null;
    public $module_id = null;
    public $url_correct = null;




    /**
     * Установка ссылки
     *
     * @param  string|null $url
     * @return $this
     */
    public function setUrl(string|null $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Получение ссылки
     *
     * @return $this->url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Получение перенаправления ссылки
     *
     * @return string
     */
    public function getRedirect()
    {
        return $this->url_correct;
    }

    /**
     * Установка текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function setText(string|null $text)
    {
        $this->text = $text;
        return $this;
    }


    /**
     * Установка текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function setTitle(string|null $title)
    {
        $this->title = $title;
        return $this;
    }


    /**
     * Установка текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function setIcon(string|null $icon)
    {
        $this->icon = $icon;
        return $this;
    }


    /**
     * Установка текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function setModule(string|null $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Установка текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function setModuleId(string|null $module_id)
    {
        $this->module_id = $module_id;
        return $this;
    }

    /**
     * Установка текста уведомления
     *
     * @param  array $url
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $avalible_parameters = ['text', 'url', 'icon', 'title', 'module_id', 'module'];
        foreach ($avalible_parameters as $value) {
            if (isset($parameters[$value]) && !empty($parameters[$value])) {
                $this->$value = $parameters[$value];
            } else {
                $this->$value = null;
            }
        }
        return $this;
    }


    /**
     * Получение текста уведомления
     *
     * @param  string|null $url
     * @return $this
     */
    public function getText()
    {
        return $this->text;
    }
}
