<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings;

use Boot\Support\Facades\Cache;
use Boot\System\Settings\Models\Settings;

trait HasSettings {

    public function _settings()
    {
        return $this->hasMany('Boot\System\Settings\Models\Settings', 'uid');
    }

    // get setting value
    public function settings($key, $default = null)
    {
        $settings = $this->getCache();
        $data = $this->searchSetting($key, $settings);   
        if(isset($data['value'])){
            $value = $data['value'];
            $type = $data['type'];
            switch($type) {
                case null:
                    return $value ?? $default;
                case 'string':
                    return (string) $value;
                case 'boolean':
                    return (bool) $value;
                case 'integer':
                    return (int) $value;
            }
        } else {
            return $default;
        }
       
    }

    private function searchSetting($key, $array, $column = 'name')
    {
        foreach ($array as $k => $val) {
        if ($val[$column] == $key) {
            return $val;
        }
    }
        return null;
    }

    // create-update setting
    public function setSetting($key, $value, $type = 'string')
    {
        $this->storeSetting($key, $value, $type);
        $this->setCache();
    }

    // create-update multiple settings at once
    public function setSettings($data = [])
    {
        foreach($data as $setting)
        {
            $key = $setting['key'];
            $value = $setting['value'];
            $type = isset($setting['type']) ? $setting['type'] : 'string';
            $this->storeSetting($key, $value, $type);
        }
        $this->setCache();
    }

    private function storeSetting($key, $value, $type)
    {
        $record = Settings::where(['uid' => $this->id, 'name' => $key])->first();
        if($record)
        {
            $record->value = $value;
            $record->type = $type;
            $record->save();
        } else {
            $data = new Settings(['name' => $key, 'value' => $value, 'type' => $type]);
            $this->_settings()->save($data);
        }
    }

    private function getCache()
    {
        if (Cache::has('user_settings_' . $this->id))
        {
            return Cache::get('user_settings_' . $this->id);
        }
        return $this->setCache();
    }

    private function setCache()
    {
        if (Cache::has('user_settings_' . $this->id))
        {
            Cache::forget('user_settings_' . $this->id);
        }
        $settings = Settings::where('uid',$this->id)->get()->toArray();
        Cache::forever('user_settings_' . $this->id, $settings);
        return $this->getCache();
    }

}
