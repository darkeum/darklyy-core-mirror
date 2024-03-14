<?php

namespace Boot\System\Grid;

class GridParams
{

    public function columns($cols)
    {
        $columns = collect();
        foreach ($cols as $key => $col) {
            $columns->put('title', $col['title'] ?? '');
            $columns->put('label', $col['label'] ?? '');
            $columns->put('name', $col['name'] ?? '');
            $columns->put('width', $col['width']);
            $columns->put('visible', $col['visible'] ? 'true' : 'false' );
        }

        return $columns->toJson();
    }

}