<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Task\Support;

use Task\Models\Task;
use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskSupport
{
    protected static function getTaskKey($type, $uid = null)
    {
        return 'task.user.' . $uid . ($type ? '.' . $type : '');
    }

    public static function getTaskCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('count', $uid);
        return Cache::rememberForever($key, function () use ($uid) {
            return Task::where("toid", $uid)->where("status", "!=", 3)->count();
        });
    }

    public static function reloadTaskCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('count', $uid);
        Cache::forget($key);
        return static::getTaskCount($uid);
    }

    public static function getTaskExpiredCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('expired.count', $uid);
        return Cache::rememberForever($key, function () use ($uid) {
            return Task::where("toid", $uid)->where("status", "!=", 3)->where('date_end', '<', Carbon::now())->count();
        });
    }

    public static function reloadTaskExpiredCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('expired.count', $uid);
        Cache::forget($key);
        return static::getTaskExpiredCount($uid);
    }

    public static function getTaskTodayCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('today.count', $uid);
        return Cache::rememberForever($key, function () use ($uid) {
            return Task::where("toid", $uid)->where("status", "!=", 3)->where(DB::raw('DATE(date_end)'), Carbon::now()->format('d.m.Y'))->count();
        });
    }

    public static function reloadTaskTodayCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getTaskKey('today.count', $uid);
        Cache::forget($key);
        return static::getTaskTodayCount($uid);
    }
}
