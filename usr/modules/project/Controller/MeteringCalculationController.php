<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Str Helper class
*/

namespace Project\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Validation\Validator;
use \Order\Model\Order;
use \Order\Model\OrderHeat;
use \Order\Model\OrderPump;
use \Order\Model\OrderPumpStation;
use \Order\Model\OrderMeteringStation;
use \Order\Model\OrderBlock;
use \Order\Model\OrderValve;
use \Calculation\MeteringCalculation;

class MeteringCalculationController extends AbstractController
{


    public static function metering_station($route = null)
    {
        try {
            $mc = new MeteringCalculation;
            $mc->makeRequest();
            $mc->init();
          
            // $mc->printOut();
            // $mc->generateDxf();
            $mc->makeProject();
        } catch (\Exception $th) {
          echo json_encode(['error' => $th->getMessage()]);
        }
      

  
    
    }

}
