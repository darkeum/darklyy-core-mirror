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

namespace Project\Tasks\Calculation\Valve;

use Boot\Abstracts\Tasks\Task;

class ValveCalculationTask extends Task
{
    public function run(array $data): array
    { 
        $result = [];

        $dn = $this->consumptionDN($data['consumption']);
        $result['dn'] = $dn;
        
        $kv = $this->consumptionKV($data['consumption'], $data['pressure_drop']);
        $result['kv'] = $kv;

        $deltaRF = $this->consumptionRF($data['consumption']);
        $result['deltaRF'] = $deltaRF;

        

        $MediumTemperature = [
            150  =>	3.86,
            145  =>	3.24,
            140  =>	2.69,
            135  =>	2.19,
            130  =>	1.76,
            125  =>	1.37,
            120  =>	1.03,
            115  =>	0.72,
            110  =>	0.46,
            105  =>	0.23,
            100  =>	0.03,
            95   => -0.14,
            90   => -0.29,
            85   => -0.41,
            80   => -0.52,
            75   => -0.61,
            70   => -0.68,
            65   => -0.75,
        ];

        // $a = 0;
        // foreach ($MediumTemperature as $key => $value)
        // {
        //     if($key == $data['t'])
        //     {
        //         $a = $value;
        //         break;
        //     }
        // }
        // $result['aaaa'] = $a;
        

        $currentValue = 0;
        $currentKey = false;
        foreach ($MediumTemperature as $key => $value) {
            if ($currentKey == false ) {
                $currentKey = $key;
                $currentValue = $value;
                $raz = abs($key - $data['t']);
            }
            if (abs($key - $data['t']) < $raz) {
                $currentKey = $key;
                $currentValue = $value;
                $raz = abs($key - $data['t']);
            } 
        }
        // $result['currentKey'] = $currentKey;
        // $result['currentValue'] = $currentValue;


        $Rpred = $this->consumptionRpred($data['pressure_before'], $currentValue);
        $result['Rpred'] = $Rpred;

        return $result;
    }

    protected function consumptionDN($V)
    {
        $G = 3;
        // $V = 7.35;
        $dn = 18.8 * sqrt($V / $G);
        return $dn;
    }
    
    
    protected function consumptionKV($x, $y)
    {
        // $x = 7.3;
        // $y = 0.3;
        $kv = $x / (sqrt($y));
        return $kv;
    }
    
    protected function consumptionRF($x)
    {
        $q = $x / 16;
        $deltaRF = pow($q, 2);
        return $deltaRF;
    }

    protected function consumptionRpred($c, $v)
    {
        $Rpred = 0.5 * ($c-$v);
        return $Rpred;
    }
}