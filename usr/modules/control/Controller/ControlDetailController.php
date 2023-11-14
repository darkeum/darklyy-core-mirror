<?php

namespace Control\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Http\Request;
use \Control\Grid\IndexGrid;
use Control\Model\Control;
use Control\Model\ControlDetail;

class ControlDetailController extends AbstractController{
    // static function index(){
        
    // }

    // static function create(){

    // }

    // static function store(Request $request){

    // }

    // static function show($id){
        
    // }

    // static function edit($id){
        
    // }

    static function update(Request $request, $id){
        
    }

    static function destroy($id){
        $control_detail = ControlDetail::findOrFail($id);
        $control_detail->delete();
        
        Control::re_total($control_detail->control_id);

        echo $control_detail->toJson();
    }
}