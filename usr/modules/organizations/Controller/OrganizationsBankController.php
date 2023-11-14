<?php

namespace Organizations\Controller;

use Boot\System\Http\Request;

use Organizations\Model\Organizations;
use \Boot\Controller\AbstractController;
use Organizations\Model\OrganizationsBank;

class OrganizationsBankController extends AbstractController{

    static function show($id){
        $organization = Organizations::findOrFail($id);
        display('@organizations/manager/edit/edit-bank-accounts-list.html.twig', [
            'id' => $id,
            'organization' => $organization,
        ]);
    }
    
    static function store(Request $request){
        echo OrganizationsBank::create($request->all())->toJson();
    } 

    static function update($id, Request $request){
        $model = OrganizationsBank::find($id);
        $model->update($request->all());
        echo $model->toJson();
    }

    static function destroy($id){
        $model = OrganizationsBank::find($id);
        $model->delete();
        echo $model->toJson();
    }
}