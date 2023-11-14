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

use Boot\Abstracts\Actions\Action;
use Project\Tasks\Calculation\Pump\PumpOptionsTask;
use Project\UI\API\Requests\Calculation\PumpModelsSearchRequest;

class ApiPumpModelsSearchAction extends Action
{
    public function run(PumpModelsSearchRequest $request): array
    {
        $data = $request->sanitizeInput([
            'ids',
        ]);
        $options = app(PumpOptionsTask::class)->run();
        $models = [];
        foreach (explode(',', $data['ids']) as $id) {
            if (isset($options['models'][$id])) {
                foreach ($options['models'][$id] as $value) {
                    $models[] = $value;
                }
            }
        }
        sort($models);
        return ['models' =>  $models];
    }
}
