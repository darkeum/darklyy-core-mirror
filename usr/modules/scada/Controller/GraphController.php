<?php

namespace Scada\Controller;

use \Boot\Controller\AbstractController;

class GraphController extends AbstractController{
    static function index(){
        display('@scada/manager/graph/index.html.twig', []);
    }
}