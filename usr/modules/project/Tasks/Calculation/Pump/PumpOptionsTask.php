<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Task
*/

namespace Project\Tasks\Calculation\Pump;

use Boot\Abstracts\Tasks\Task;
use Boot\Support\Facades\Cache;
use Boot\Support\Facades\Config;

class PumpOptionsTask extends Task
{
    public function run($usePumpStation = false): array
    {
        $result = Cache::remember('pump_options', now()->addDays(1), function () {
            $pump = Config::get('project-pump.models.pump')::with('catalog')->get();
            $modelAttribute = Config::get('project-pump.attributes.model');
            $availableManufacture = [];
            $availableModels = [];
            foreach ($pump as $value) {
                $availableManufacture[$value->catalog->man] = $value->catalog->man;
                if (isset($value->catalog->attribute[$modelAttribute]) && !empty($value->catalog->attribute[$modelAttribute])) {
                    $model = $value->catalog->attribute[$modelAttribute];
                    $availableModels[$value->catalog->man][$model] = ['id' => $model, 'title' => $model];
                }
            }
            $manufacture = Config::get('project-pump.models.manufacture')::whereIn('id', $availableManufacture)->get();
            $availableManufacture = [];
            foreach ($manufacture as $value) {
                $availableManufacture[] = [
                    'id' => $value->id,
                    'title' => $value->name
                ];
            }

            return [
                'manufacture' => $availableManufacture,
                'models' => $availableModels,
                'destination' => Config::get('project-pump.destination'),
            ];
        });
        if ($usePumpStation) {
            $availableModels = [];
            foreach ($result['manufacture'] as $key => $value) {
                if (in_array($value['id'], [20,270,271])) {
                    $availableModels[] = $value;
                }
            }
            $result['manufacture'] = $availableModels;
        }
        return  $result;
    }
}
