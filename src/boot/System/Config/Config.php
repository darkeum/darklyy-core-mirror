<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Config class 
*/

namespace Boot\System\Config;

class Config
{
    protected function getConfig($patch)
    {
        $configFile = ROOT . 'config/' . $patch . '.config.php';
        if (file_exists($configFile)) {
            return require($configFile);
        } else {
            throw new \RuntimeException('Undefined config file.');
        }
    }

    public function get($key, $value = null)
    {
        $temp = explode('.', $key);
        if (count($temp) == 2) {
            $patch =  $temp[0];
            $parameter =  $temp[1];
            $configArray = $this->getConfig($patch);
            if (isset($configArray[$parameter])) {
                return $configArray[$parameter];
            } else {
                if ($value !== null) {
                    return $value;
                } else {
                    throw new \RuntimeException('Undefined parameter');
                }                
            }
        } elseif(count($temp) == 1) {
            return $this->getConfig($key);
            
        }
    }

    public function all($patch)
    {
        return $this->getConfig($patch);
    }

    public function has($key)
    {
        $temp = explode('.', $key);
        if (count($temp) == 2) {
            $patch =  $temp[0];
            $parameter =  $temp[1];
            $configArray = $this->getConfig($patch);
            if (isset($configArray[$parameter])) {
                return true;
            }
        }
        return false;
    }
}
