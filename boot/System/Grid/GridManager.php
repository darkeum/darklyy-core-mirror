<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid;

use Exception;
use Boot\System\Grid\GridFinder;
use Boot\System\Livewire\Exceptions\ComponentNotFoundException;

class GridManager
{
    public $componentAliases;
    
    /**
     * Монтирование указанного Grid`а
     *
     * @param  mixed $name
     * @param  mixed $params
     * @return void
     */
    public function mount($name, $params = [], $operation = 'render', $request = [])
    {
        if (is_string($params)) $params = [];
        $id = str()->random(20);
        if (class_exists($name)) {
            $name = $name::getName();
        }
        return $this->getInstance($name, $id, $operation, $request);
    }

    /**
     * Поиск класса Grid
     *
     * @param  mixed $alias
     * @return string
     */
    public function getClass($alias)
    {
        $finder = app(GridFinder::class);

        $class = false;

        $class = $class ?: ($this->componentAliases[$alias] ?? $finder->find($alias)
        );

        $class = $class ?: ($finder->build()->find($alias)
        );

        throw_unless($class, new ComponentNotFoundException(
            "Не удалось найти указанный Grid: [{$alias}]"
        ));

        return $class;
    }

    /**
     * Получение состояний Grid и выполнение запрашиваемой операции
     *
     * @param  mixed $component
     * @param  mixed $id
     * @return void
     */
    public function getInstance($component, $id, $operation, $request)
    {
        $componentClass = $this->getClass($component);

        throw_unless(class_exists($componentClass), new ComponentNotFoundException(
            "Указанный класс Grid'a [{$component}] не найден: [{$componentClass}]"
        ));
        $grid = new $componentClass($id);
        $grid->setGridId($component);
        switch ($operation) {
            case 'render':
                return $grid->render();
                break;
            case 'output':
                return $grid->output($request);
                break;
        }
        throw new Exception("Недопустимая операция", 1);
        
    }
}
