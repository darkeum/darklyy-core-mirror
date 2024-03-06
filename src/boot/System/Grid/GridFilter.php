<?php

namespace Boot\System\Grid;

class GridFilter
{
    public mixed $col, $cols, $default, $filters, $type;

    function __construct($c) {
        $this->col = $c; // Передоваемый столбец
        $this->default = config('grid.default');
        $this->filters = $this->default['filters'];
        $this->type = $c['type'] ?? $this->default['type'];
    }

    // Проверка если есть
    public static function check($type, $filters, $default = null)
    {
        return array_key_exists($type, $filters) ? $filters[$type] : $default;
    }

    // Фильтруем
    public static function filter(): void
    {

    }

}