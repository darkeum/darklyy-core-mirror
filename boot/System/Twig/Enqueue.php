<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Enqueue file class
*/

namespace Boot\System\Twig;

use \Boot\Support\Facades\Cache;
use \Boot\Models\TplEnqueue;

class Enqueue
{
    /**
     * Получение подключаемого файла (css, js и т.д)
     *
     * @param  array        $matches Параметры файла а именно:
     *                      1 - тип поиска id/title
     *                      2 - ключ поиска
     * @return string|null  Возвращает true если удалено успешно, иначе false записанного кэша
     */
    public static function enqueueFile($matches = [])
    {
        $data = static::getEnqueueData($matches);
        if (!empty($data)) {
            $data = static::enqueueFileCheck($data);
            switch ($data['type']) {
                default:
                    $return = $data['file'] . '?v=' . $data['cache_key'];
                    break;
                case "css":
                    if ($data['async'] == 1) {
                        $return = '<link rel="preload" as="style" onload="this.rel=\'stylesheet\'" type="text/css" href="' . $data['file'] . '?v=' . $data['cache_key'] . '" />';
                        $return .= '<noscript><link type="text/css" href="' . $data['file'] . '?v=' . $data['cache_key'] . '" rel="stylesheet" /></noscript>';
                    } else {
                        $return = '<link rel="stylesheet"  type="text/css" href="' . $data['file'] . '?v=' . $data['cache_key'] . '" />';
                    }
                    break;
                case "js":
                    $async = '';
                    if ($data['async'] == 1) {
                        $async = 'async';
                    }
                    $return = '<script type="text/javascript" src="' . $data['file'] . '?v=' . $data['cache_key'] . '" ' . $async . '></script>';
                    break;
            }
            return $return;
        }
        return null;
    }

    /**
     * Получение (запись) кэша подключаемых файлов 
     *    
     * @return array  Возвращает кэш подключаемых файлов
     */
    protected static function enqueueFileCache()
    {
        if (Cache::has('tpl.enqueue')) {
            $enqueue_array = Cache::get('tpl.enqueue');
        } else {
            $enqueue = TplEnqueue::all()->toArray();
            $enqueue_array = [];
            foreach ($enqueue as $key => $value) {
                $enqueue_array['id'][$value['id']] = $value;
                $enqueue_array['title'][$value['title']] = $value;
            }
            Cache::set('tpl.enqueue', $enqueue_array);
        }
        return $enqueue_array;
    }

    /**
     * Проверка даты изменения файлов, если файл изменен об этом ставится отметка в базе данных и перезаписывается кэш
     * 
     * @param  array  $matches Данные подключаемого файла
     * @return array  Возвращает проверенные данные подключаемого файла
     */
    protected static function enqueueFileCheck($data)
    {
        $file_modify = filectime(ROOT . $data['file']);
        if ($data['modify'] != $file_modify) {
            $version = $data['version'] + 1;
            $tpl = TplEnqueue::find($data['id']);
            if(!empty($tpl)){
                $tpl->update([
                    'modify' => $file_modify,
                    'version' => $version,
                    'cache_key' => uniqid()
                ]);
            }
           
            Cache::delete('tpl.enqueue');
            $data = static::getEnqueueData([
                1 => 'id',
                2 => $data['id']
            ]);
        }
        return $data;
    }

    /**
     * Поиск подключаемого файла по заданным параметрам
     *
     * @param  array        $matches Параметры файла а именно:
     *                      1 - тип поиска id/title
     *                      2 - ключ поиска
     * @return array|null   Возвращает данные найденного файла, иначе null
     */
    protected static function getEnqueueData($matches)
    {
        $enqueue_array = static::enqueueFileCache();
        if ($matches[1] == 'id') {
            $data = $enqueue_array['id'][$matches[2]] ?? null;
        } else {
            $data = $enqueue_array['title'][$matches[2]] ?? null;
        }
        return $data;
    }
}
