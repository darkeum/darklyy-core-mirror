<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Broadcasting;

use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UniqueBroadcastEvent extends BroadcastEvent implements ShouldBeUnique
{
    /**
     * Уникальный идентификатор блокировки.
     *
     * @var mixed
     */
    public $uniqueId;

    /**
     * Количество секунд, в течение которых должна сохраняться уникальная блокировка.
     *
     * @var int
     */
    public $uniqueFor;

    /**
     * Создайте новый экземпляр события.
     *
     * @param  mixed  $event
     */
    public function __construct($event)
    {
        $this->uniqueId = get_class($event);

        if (method_exists($event, 'uniqueId')) {
            $this->uniqueId .= $event->uniqueId();
        } elseif (property_exists($event, 'uniqueId')) {
            $this->uniqueId .= $event->uniqueId;
        }

        if (method_exists($event, 'uniqueFor')) {
            $this->uniqueFor = $event->uniqueFor();
        } elseif (property_exists($event, 'uniqueFor')) {
            $this->uniqueFor = $event->uniqueFor;
        }

        parent::__construct($event);
    }

    /**
     * Решите реализацию кэша, которая должна управлять уникальностью события.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function uniqueVia()
    {
        return method_exists($this->event, 'uniqueVia')
            ? $this->event->uniqueVia()
            : Container::getInstance()->make(Repository::class);
    }
}
