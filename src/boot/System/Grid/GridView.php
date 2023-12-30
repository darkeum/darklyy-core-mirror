<?php

namespace Boot\System\Grid;

class GridView
{
    protected $namespace;

    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param array $params
     * @return string
     * # Вывод автоматической генерации Actions
     */
    public static function actions(array $params): string
    {
        return view('grid::layouts.actions', $params)->render();
    }

    /**
     * @param array $params
     * @return string
     * # Вывод автоматической генерации Img
     */
    public static function img(array $params): string
    {
        return view('grid::layouts.img', $params)->render();
    }

    /**
     * @param $path
     * @param array $params
     * @return string
     */
    public static function view($path, array $params = []): string
    {
        return view($path, $params)->render();
    }

}
