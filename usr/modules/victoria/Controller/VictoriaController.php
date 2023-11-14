<?php

namespace Victoria\Controller;

use \Boot\Controller\AbstractController;
use \Boot\Services\Request;
use \Victoria\Victoria;

class VictoriaController extends AbstractController{

    static function index(){
		$victoria = new Victoria;
		echo $victoria->popup();
    }

    static function load_list(Request $request){
        $victoria = new Victoria;
	    $data = $victoria->grid_items_body($request->request->all());
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    static function get_item(Request $request){
        $victoria = new Victoria;
	    $data = $victoria->grid_items_basket_body($request->request->all());
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    static function filter(Request $request){
        $victoria = new Victoria;
	    $data = $victoria->filter($request->request->all());
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    static function cert(Request $request){
        $victoria = new Victoria;
	    $data = $victoria->cert($request->request->all());
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    static function attribute(Request $request){
        $victoria = new Victoria;
	    $data = $victoria->attribute($request->request->all());
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}