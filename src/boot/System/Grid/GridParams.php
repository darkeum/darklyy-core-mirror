<?php

namespace Boot\System\Grid;

class GridParams
{

    // WIP - Придумать как лучше всего сделать на PHP
    public static function columns($cols)
    {
        $columns = collect();
        foreach ($cols as $key => $col) {
            $columns->put('title', $col['title'] ?? '');
            $columns->put('label', $col['label'] ?? '');
            $columns->put('name', $col['name'] ?? '');
            $columns->put('width', $col['width']);
            $columns->put('visible', $col['visible'] ? 'true' : 'false' );
            $columns->put('className', $col['className'] ?? ''); // col-{{ $col['type'] }}

            $columns->put('render', "render: function (data, type, row, meta) {return grid.render(data, type, row, meta, '". $col['render'] ."')},");
        }

        return $columns->toJson(JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
    }

}