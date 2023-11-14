<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\Support;

use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Cache;
use Notifications\Models\Notifications;
use Notifications\Exceptions\TypesException;

class NotificationsSupport
{
    protected static function getNotificationKey($type, $uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        return 'notifications.user.' . $uid . ($type ? '.' . $type : '');
    }

    public static function getNotificationsCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getNotificationKey('count', $uid);
        return Cache::rememberForever($key, function () use ($uid) {
            return Notifications::where('status', 1)->where('uid', $uid)->count();
        });
    }

    public static function reloadNotificationsCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getNotificationKey('count', $uid);
        Cache::forget($key);
        return static::getNotificationsCount($uid);
    }

    public static function getNotificationsBar($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = 'notifications.user.' . $uid;
        return Cache::rememberForever($key, function () use ($uid) {
            return Notifications::where('status', 1)->where('uid', $uid)->limit(20)->get();
        });
    }

    public static function getNotificationModule($id = null, $type = 'default')
    {
        $current_module = [
            'assembly' => [
                'title' => 'Сборка товаров'
            ],
            'boiler_bid' => [
                'title' => 'Заявка в котле'
            ],
            'boiler_spec' => [
                'title' => 'Заявка на спец. условие'
            ],
            'calculation' => [
                'title' => 'Расчеты'
            ],
            'catalog' => [
                'title' => 'Каталог'
            ],
            'chat' => [
                'title' => 'Чат'
            ],
            'fcontrol_pay' => [
                'title' => 'Заявка на оплату'
            ],
            'fcontrol_storage' => [
                'title' => 'Подтверждение остатков'
            ],
            'god' => [
                'title' => 'Проведение в минус'
            ],
            'logistics' => [
                'title' => 'Логистика'
            ],
            'order' => [
                'title' => 'Заявки и обращения'
            ],
            'price' => [
                'title' => 'Прайсы'
            ],
            'request' => [
                'title' => 'Расчеты'
            ],
            'sales' => [
                'title' => 'Продажи'
            ],
            'storage' => [
                'title' => 'Пересчет'
            ],
            'supply' => [
                'title' => 'Заявки на закупку'
            ],
            'supply_logistics' => [
                'title' => 'Доставка товаров'
            ],
            'supply_order' => [
                'title' => 'Счет поставщика'
            ],
            'task' => [
                'title' => 'Задачи'
            ],
            'test' => [
                'title' => 'Сборка товаров'
            ],
            'twodetail' => [
                'title' => 'Актуализация данных'
            ],
        ];
        $rows = [];
        $rows['default'] = [];
        foreach ($current_module as $key => $value) {
            $rows['default'][$key] = $value['title'];
        }
        if (empty($id)) {
            if (isset($rows[$type])) {
                return $rows[$type];
            } else {
                throw new TypesException();
            }
        } else {
            if (isset($rows[$type][$id])) {
                return $rows[$type][$id];
            } else {
                throw new TypesException();
            }
        }
    }
}
