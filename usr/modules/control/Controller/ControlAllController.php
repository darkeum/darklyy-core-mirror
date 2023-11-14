<?php

namespace Control\Controller;

use \Boot\Controller\AbstractController;
use \Control\Grid\ControlAllGrid;

class ControlAllController extends AbstractController{

    public function __construct()
    {
        $this->middleware(function($request, $next){
            $uid = \Boot\Support\Facades\Auth::id();
            $group = getGroup(["group" => ["accountant", "admin"]]);
            if(in_array($uid, $group)){
                return $next($request);
            }
            if(\Organizations\Model\Org::where("director", \Boot\Support\Facades\Auth::id())->doesntExist()){
                return redirect('admin');
            }
            
            return $next($request);
        });
    }

    static function index(){
        $grid = new ControlAllGrid;
        display('@control/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }
}