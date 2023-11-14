<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\System\Notifications;

use \Boot\System\Database\Model\Builder;
use \Boot\System\Database\Model\Model;

class DatabaseNotification extends Model
{

    /**
     * Имя таблицы с уведомлениями.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * Защищенные атрибуты модели.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Атрибуты, которые должны быть приведены к указанным типам.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Получение объекта, к которому принадлежит уведомление.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Установка отметки уведомления как прочитанное.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Установка отметки уведомления как не прочитанное.
     *
     * @return void
     */
    public function markAsUnread()
    {
        if (! is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Определение, было ли прочитано уведомление.
     *
     * @return bool
     */
    public function read()
    {
        return $this->read_at !== null;
    }

    /**
     * Определение, было ли не прочитано уведомление.
     *
     * @return bool
     */
    public function unread()
    {
        return $this->read_at === null;
    }

    /**
     * Настройте область запроса, чтобы включить только уведомления о прочтении.
     *
     * @param  \Boot\System\Database\Model\Builder  $query
     * @return \Boot\System\Database\Model\Builder
     */
    public function scopeRead(Builder $query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Настройте область запроса, чтобы включить только непрочитанные уведомления.
     *
     * @param  \Boot\System\Database\Model\Builder  $query
     * @return \Boot\System\Database\Model\Builder
     */
    public function scopeUnread(Builder $query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Создайте новый экземпляр коллекции уведомлений базы данных.
     *
     * @param  array  $models
     * @return \Boot\System\Notifications\DatabaseNotificationCollection
     */
    public function newCollection(array $models = [])
    {
        return new DatabaseNotificationCollection($models);
    }
}
