<?php

namespace Boot\System\Grid;

class GridFilter
{
    public mixed $col, $cols, $default, $filters, $type;

    function __construct($c) {
        $this->default = config('grid.default');
        $this->col = $c;
        $this->cols = $this->default['cols'];
        $this->filters = $this->default['filters'];
        $this->type = $c['type'] ?? $this->default['type'];
    }

    # Функция проверки
    public function check($type, $filters, $default = null)
    {
        return array_key_exists($type, $filters) ? $filters[$type] : $default;
    }

    // Фильтруем
    public function filter(): void
    {
        # Что фильтруем?
        foreach ($this->filters as $fi) {
            if (!isset($this->col[$fi])) {
                $x = $this->cols[$fi];
                $this->col[$fi] = $this->check($this->type, $x, current($x));
            }
        }
    }

}