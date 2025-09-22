<?php

namespace Boot\System\Grid;

use Boot\System\Http\Request;
use Throwable;

//use phpDocumentor\Reflection\Types\Collection;
//use ReflectionClass;

class GridView
{
    protected $namespace;

    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param  array  $params
     * @return string
     * # Вывод автоматической генерации Actions в Grid
     * @throws Throwable
     */
    public static function actions(array $params): string
    {
        // Фильтруем от false, если ЕСТЬ то убираем
        $params['actions'] = array_filter($params['actions'], function ($elem) {
            return $elem == true;
        });

        //$params['actions_params'] = [
        //    'config' => config('grid.actions'), // Переделать на мультиязычность
        //    //'keys' => array_keys($params['actions']),
        //    'count' => count($params['actions'])
        //];
        $params['actions_cfg'] = config('grid.actions'); // Переделать на мультиязычность
        $params['actions_keys'] = array_keys($params['actions']);
        $params['actions_count'] = count($params['actions']);

        //$params['destroy'] = array_search('destroy', array_keys($params['actions']), true);
        if (array_key_exists('destroy', $params['actions'])) {
            $params['destroy'] = $params['actions']['destroy'];
        }

        return view('grid::layouts.actions', $params)->render();
    }

    /**
     * @param  array  $params
     * @return string
     * # Вывод автоматической генерации Img в Grid
     * @throws Throwable
     */
    public static function img(array $params): string
    {
        return view('grid::layouts.img', $params)->render();
    }

    /**
     * @param $path
     * @param  array  $params
     * @return string
     * @throws Throwable
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

    # Select2
    public static function select2(Request $request)
    {
        $model = $request->query('model');
        $col = $request->query('col');
        $query = $request->query('query');

        $result = $model::query()
            ->where([])
            ->where($col, 'LIKE', "%{$query}%")
            //->orWhere('or_desc', 'LIKE', "%{$text}%")
            ->get();

        //dd($result->pluck($col, $col)->toJson(JSON_UNESCAPED_UNICODE));
        $result = $result->pluck($col, $col);
        //$result->prepend(__('grid.all'), "__ALL_SELECT_GRID");
        //dd($result);

        return $result->toArray();
    }

    # Генерация иконки в Grid
    public static function icon($type, $css, $title = '')
    {
        switch ($type) {
            case 'fe':
                $css = '<i class="feather-'.$css.'" title="'.$title.'"></i>';
                break;
            case 'fa':
                $css = '<i class="'.$css.'" title="'.$title.'"></i>';
                break;

            // case 'badge':
            //     $css = '<i class="' . $css . '" title="' . $title . '"></i>';
            //     break;

            default:
                $css = '<i class="'.$css.'" title="'.$title.'"></i>';
                break;
        }

        return $css;

    }

    # Вернуть html tag
    public static function tag($tag = 'div', $class = '', $html = '', $tooltip = false)
    {
        if ($tooltip) {
            $tooltip = " data-bs-toggle='tooltip' title='{$tooltip}'";
        }

        return "<{$tag} class='{$class}'{$tooltip}>{$html}</{$tag}>";
    }

    # Вернуть Badge
    public static function badge($color = '', $html = '', $tag = 'span')
    {
        return self::tag($tag, "badge {$color}", $html);
    }

}
