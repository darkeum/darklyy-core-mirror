<?php

namespace Boot\System\Grid;

class GridView
{
    protected $namespace;

    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    // Вывод автоматической генерации Actions
    public function actions($params): string
    {
        return view('grid::layouts.actions', $params)->render();
    }

}
