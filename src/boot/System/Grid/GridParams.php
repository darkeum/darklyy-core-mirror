<?php

namespace Boot\System\Grid;

class GridParams
{
    // WIP - Придумать как лучше всего сделать на PHP
    public static function columns2($cols)
    {
        $columns = collect();
        foreach ($cols as $key => $col) {
            $columns->put('title', $col['title'] ?? '');
            $columns->put('label', $col['label'] ?? '');
            $columns->put('name', $col['name'] ?? '');
            $columns->put('width', $col['width']);
            $columns->put('visible', $col['visible'] ? 'true' : 'false');
            $columns->put('className', $col['className'] ?? ''); // col-{{ $col['type'] }}

            $columns->put('render',
                "render: function (data, type, row, meta) {return grid.render(data, type, row, meta, '".$col['render']."')},");
        }

        return $columns->toJson(JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
    }

    public static function columns($cols)
    {
        $columns = [];

        foreach ($cols as $key => $col) {
            $column = [
                'title' => $col['title'] ?? '',
                'label' => $col['label'] ?? '',
                'name' => $col['db'] ?? ($col['name'] ?? ''),
                'width' => $col['width'] ?? '',
                'visible' => $col['visible'] ?? false ? true : false,
                'className' => ($col['className'] ?? '').' col-'.($col['type'] ?? ''),
                'sortable' => isset($col['sortable']) ? (bool) $col['sortable'] : true,
                'searchable' => isset($col['search']) ? ($col['search'] !== false) : false,
                'data' => $col['name'] ?? ''
            ];

            // Добавляем опциональные поля
            if (isset($col['other'])) {
                $column['other'] = $col['other'];
            }

            if (isset($col['default'])) {
                $column['default'] = $col['default'];
            }

            if (isset($col['options'])) {
                $column['options'] = $col['options'];
            }

            if (isset($col['priority'])) {
                $column['priority'] = $col['priority'];
                $column['responsivePriority'] = $col['priority'];
            }

            // Добавляем render функцию если нужно
            if (!empty($col['render'])) {
                $column['render'] = "function (data, type, row, meta) { return grid.render(data, type, row, meta, '{$col['render']}')}";
            }

            // Добавляем configs объект
            $column['configs'] = [
                'icon' => $col['icon'] ?? null,
                'type' => $col['type'] ?? null,
                'prefix' => $col['prefix'] ?? null,
                'render' => $col['render'] ?? null
            ];

            if (isset($col['format_date'])) {
                $column['configs']['displayFormat'] = $col['format_date'];
            }

            if (isset($col['tooltip'])) {
                $column['configs']['tooltip'] = $col['tooltip'];
            }

            if (isset($col['popover']) && !empty($col['render'])) {
                $column['configs']['popover'] = $col['popover'];
            }

            $columns[] = $column;
        }

        return json_encode($columns, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }

    // WIP -
    public static function ignored($type): bool
    {
        $arr = config('grid.ignored');
        return in_array($type, $arr);
    }

    # ...
    public static function param($col, $param)
    {
        return "{$param}: '{$col[$param]}',";
    }

    # ...
    public static function paramHas($col, $param, $custom = false, $mirror = false)
    {
        $custom = $custom ? $custom : $param;
        $mirror = $mirror ? $custom : $param;

        return isset($col[$param]) ? "{$custom}: '{$col[$mirror]}'," : false;
    }

    # ...
    public static function paramJS($col, $param, $code)
    {
        return isset($col[$param]) ? "{$param}: '{$col[$param]}'," : false;
    }

    // WIP -
    public static function exportOptions($cols)
    {
        $result = [];
        foreach ($cols as $key => $col) {
            if ($col['visible'] == true || $col['visible'] == 'true') {
                if ($col['excel_hidden'] != true || $col['excel_hidden'] != 'true') {
                    $result[] = $key;
                }
            } else {
                if ($col['excel_only'] == true || $col['excel_only'] == 'true') {
                    $result[] = $key;
                }
            }
        }

        return implode(', ', $result);

    }

}
