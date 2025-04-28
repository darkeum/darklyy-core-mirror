<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings;

use Boot\Support\Facades\Cache;
use Boot\System\Settings\Models\Settings;

trait HasSettings
{

    /**
     * Связь настроек с базой данных пользователя
     *
     * @return object
     */
    public function _settings()
    {
        return $this->hasMany('Boot\System\Settings\Models\Settings', 'uid');
    }

    /**
     *  Получение настроек пользователя
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function settings(string $key, mixed $default = null)
    {
        $settings = $this->getCache();
        $data = $this->searchSetting($key, $settings);
        if (isset($data['value'])) {
            $value = $data['value'];
            $type = $data['type'];
            switch ($type) {
                case null:
                case 'null':
                    return $value ?? $default;
                case 'string':
                    return (string) $value;
                case 'boolean':
                    return (bool) $value;
                case 'integer':
                    return (int) $value;
                case 'double':
                case 'float':
                    return (float) $value;
                case 'array':
                    return json_decode($value, true);
                case 'object':
                    return json_decode($value);
            }
        } else {
            return $default;
        }
    }

    /**
     * Поиск необходимой настройки
     *
     * @param  string $key
     * @param  array $array
     * @param  string $column
     * @return mixed
     */
    private function searchSetting(string $key, array $array, string $column = 'name')
    {
        foreach ($array as $val) {
            if ($val[$column] == $key) {
                return $val;
            }
        }
        return null;
    }


    /**
     * Установка значения настроек
     *
     * @param  string $key Ключ настроек
     * @param  mixed $value Значение настроек
     * @param  string $type Тип значения настроек, если unknown то определяется автоматически
     * @return void
     */
    public function setSetting(string $key, mixed $value, string $type = 'unknown')
    {
        $this->storeSetting($key, $value, $type);
        $this->setCache();
    }


    /**
     * Массовая установка настроек
     *
     * @param  array $data
     * @return void
     */
    public function setSettings(array $data = [])
    {
        foreach ($data as $setting) {
            $key = $setting['key'];
            $value = $setting['value'];
            $type = isset($setting['type']) ? $setting['type'] : 'unknown';
            $this->storeSetting($key, $value, $type);
        }
        $this->setCache();
    }

    /**
     * Сохранение настроек в базе данных
     *
     * @param  string $key
     * @param  mixed $value
     * @param  string $type
     * @return void
     */
    private function storeSetting(string $key, mixed $value, string $type)
    {
        $record = Settings::where(['uid' => $this->id, 'name' => $key])->first();
        if ($type === 'unknown') {
            switch (gettype($value)) {
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':
                case 'array':
                case 'object':
                case 'resource':
                    $type = gettype($value);
                    break;
                case 'NULL':
                case null:
                    $type = null;
                    break;
                default:
                    $type = 'unknown';
                    break;
            }
        }
        if ($type === 'array' || $type === 'object') {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($record) {
            $record->value = $value;
            $record->type = $type;
            $record->save();
        } else {
            $data = new Settings(['name' => $key, 'value' => $value, 'type' => $type]);
            $this->_settings()->save($data);
        }
    }

    /**
     * Получение ключа кеша
     *
     * @return string
     */
    private function cacheKey()
    {
        return 'user_settings_' . $this->id;
    }

    /**
     * Получение кеша настроек пользователя
     *
     * @return array
     */
    private function getCache()
    {
        if (Cache::has($this->cacheKey())) {
            return Cache::get($this->cacheKey());
        }
        return $this->setCache();
    }

    /**
     * Установка кеша настроек пользователя
     *
     * @return array
     */
    private function setCache()
    {
        if (Cache::has($this->cacheKey())) {
            Cache::forget($this->cacheKey());
        }
        $settings = Settings::where('uid', $this->id)->get()->toArray();
        Cache::forever($this->cacheKey(), $settings);
        return $this->getCache();
    }
}
