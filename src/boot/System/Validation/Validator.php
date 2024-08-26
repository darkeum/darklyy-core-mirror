<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Validation;

use Boot\Support\Str;
use Illuminate\Validation\Validator as LaravelValidator;
use Boot\Contracts\Validation\Validator as ValidatorContract;

class Validator extends LaravelValidator implements ValidatorContract
{
    protected $modifedData;

    public function getModifidData()
    {
        return $this->modifedData;
    }

    public function getModifidField(string $key)
    {
        return isset($this->modifedData[$key]) ? $this->modifedData[$key] : (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    public function setModifidField($key, $value)
    {
        $this->modifedData[$key] = $value;
    }

    public function setModifidData($modifedData)
    {
        $this->modifedData = $modifedData;
    }

    /**
     * Call a class based validator extension.
     *
     * @param  string  $callback
     * @param  array  $parameters
     * @return bool
     */
    protected function callClassBasedExtension($callback, $parameters)
    {
        [$class, $method] = Str::parseCallback($callback, 'validate');

        $container = $this->container->make($class);
        $result = $container->{$method}(...array_values($parameters));
        // dd($parameters);
        // if (method_exists($container, 'modifi')) {
        //     dd('d');
        // } else {
        //     dd('s');
        // }
        // dd($container);
        // dd($result);
        return $result;
    }
}
