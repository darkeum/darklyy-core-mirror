<?php

namespace Selection\Support;

class SelectionRequestSupport{
    static function create($id, $type){
        $selection = \Selection\Model\SelectionRequest::find($id)->toArray();
        // dd($selection);
        $request = array(
            'oid' => $selection['oid'],
            'object' => $selection['object'],
            'org' => '1',
            'uid' => \Boot\Support\Facades\Auth::id()
        );
        $sales = \Sales\Model\Sales::create($request);
        $sid = $sales->id;
          //["sales_id" => "", "sales_detail" => [ ["cid" => ""], [] ]]
        $requestDetail = array(
            "sales_id" => $sid,
            "sales_detail" => array()   
        );
        $SelectionRequest = new \Selection\Controller\Request(); 
        $SelectionRequest->setRequestId($id);
		$SelectionRequest->getSelectionRequest();
		$SelectionRequest->getCalculationData();
		$blockData = $SelectionRequest->getInputData();
        // dd($blockData);
        $sales_detail = array();
        foreach($blockData as $key => $value) {
            foreach($value['spec'] as $keyS => $valueS) {
                $sales_detail[] = array(
                    'cid' => $valueS['tm_cid'],
                    'count' => $valueS['tm_count']
                );
            } 
        }
        $requestDetail['sales_detail'] = $sales_detail;
        \Sales\Support\SalesDetailSupport::mass_add($requestDetail);
        header("Location: https://atri-energo.ru/administration/module/sales/order/".$sid);
        exit;
        // dd($sid);

    }
}