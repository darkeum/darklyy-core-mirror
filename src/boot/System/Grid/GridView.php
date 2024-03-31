<?php

namespace Boot\System\Grid;

use phpDocumentor\Reflection\Types\Collection;
use Throwable;

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
     * # Вывод автоматической генерации Actions в Grid
     */
    public static function actions(array $params): string
    {
        // Фильтруем от false, если ЕСТЬ то убираем
        $params['actions'] = array_filter($params['actions'], function ($elem) {
            return $elem == true;
        });

        $params['actions_params'] = [
            'config' => config('grid.actions'), // Переделать на мультиязычность
            //'keys' => array_keys($params['actions']),
            'count' => count($params['actions'])
        ];

        $params['actions_cfg'] = config('grid.actions'); // Переделать на мультиязычность
        $params['actions_keys'] = array_keys($params['actions']);
        $params['actions_count'] = count($params['actions']);
        $params['destroy'] = array_search('destroy', array_keys($params['actions']));


        return view('grid::layouts.actions', $params)->render();
    }

    /**
     * @param array $params
     * @return string
     * # Вывод автоматической генерации Img в Grid
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

    /**
     * @return string
     * # Модификаторы в Grid
     * @throws Throwable
     */
    public static function mods(): string
    {
        return view('grid::layouts.toolbar.mods.list')->render();
    }

    public static function icon($type, $css, $title = false)
    {
        switch ($type) {
            case 'fe':
                $css = '<i class="feather-' . $css . '" title="' . $title . '"></i>';
                break;
            case 'fa':
                $css = '<i class="' . $css . '" title="' . $title . '"></i>';
                break;

            // case 'badge':
            //     $css = '<i class="' . $css . '" title="' . $title . '"></i>';
            //     break;

            default:
                $css = '<i class="' . $css . '" title="' . $title . '"></i>';
                break;
        }

        return $css;

    }

}
