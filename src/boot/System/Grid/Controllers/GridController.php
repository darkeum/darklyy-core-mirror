<?php

namespace Boot\System\Grid\Controllers;

use Boot\System\Http\Request;

class GridController
{
    #
    public function index($id, Request $request)
    {
        return app('grid')->mount($id, [], 'output', $request);
    }

}
