<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\Actions\Calculation\Pump;

use Boot\System\Http\Request;
use Boot\Support\Facades\Config;
use Boot\Abstracts\Actions\Action;

class ApiPumpSearchAction extends Action
{
    public function run(Request $request): array
    {
        $pump = Config::get('project-pump.models.pump')::with('catalog')->whereHas('catalog', function ($q) use ($request) {
            if (!empty($request->search)) {
                $q->where('sku', 'like', '%' . $request->search . '%');
            }
        })->groupBy('cid')->paginate(40, ['*'], 'page', $request->page);
        $result = [];
        foreach ($pump as $key => $value) {
            $result['results'][] = [
                'value' => $value->catalog->id,
                'label' => $value->catalog->sku,
            ];
        }
        $result['has_more'] = $pump->hasMorePages();
        return $result;
    }
}
