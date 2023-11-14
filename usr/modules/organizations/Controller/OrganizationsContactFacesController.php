<?php

namespace Organizations\Controller;

use \Boot\Controller\AbstractController;
use \Boot\System\Http\Request;;
use Organizations\Model\Organizations;
use Boot\Models\Users;
use \Boot\Models\UsersOrg;

class OrganizationsContactFacesController extends AbstractController{

    static function show($id){
        $organization = Organizations::where("id", $id)->with("users.user")->first();
        display('@organizations/manager/edit/edit-contact-faces-list.html.twig', [
            'id' => $id,
            'organization' => $organization,
        ]);
    }
    
    static function store(Request $request){
        if($request->has("uid") && empty($request->get("uid"))){
            $user = new Users;
            $email = $request->get("email");
            $user->nick = $email;
            $user->email = $email;
            $user->password = md5(time());
            $user->tail = md5(time());
            $user->phone = $request->get("phone");
            $user->phone2 = $request->get("phone2");
            $user->surname = $request->get("surname");
            $user->name = $request->get("name");
            $user->ochestvo = $request->get("ochestvo");
            $user->group = 2;
            $user->active = 1;
            $user->save();
            $uid = $user->id;
        } else {
            $uid = $request->get("uid");
        }
        $UsersOrg = new UsersOrg;
        $UsersOrg->uid = $uid;
        $UsersOrg->oid = $request->get("oid");
        $UsersOrg->post = $request->get("post");
        $UsersOrg->comment = $request->get("comment");
        $UsersOrg->type = $request->get("type");
        $UsersOrg->save();
        echo $UsersOrg->toJson();
    } 

    static function update($id, Request $request){
        $UsersOrg = UsersOrg::findOrFail($id);
        $UsersOrg->user;
        if($request->has("email")){
            $email = $request->get("email");
            $UsersOrg->user->nick = $email;
            $UsersOrg->user->email = $email;
        } 
        if($request->has("phone")) $UsersOrg->user->phone = $request->get("phone");
        if($request->has("phone2")) $UsersOrg->user->phone2 = $request->get("phone2");
        if($request->has("surname")) $UsersOrg->user->surname = $request->get("surname");
        if($request->has("name")) $UsersOrg->user->name = $request->get("name");
        if($request->has("ochestvo")) $UsersOrg->user->ochestvo = $request->get("ochestvo");
        $UsersOrg->user->save();

        if($request->has("post")) $UsersOrg->post = $request->get("post");
        if($request->has("comment")) $UsersOrg->comment = $request->get("comment");
        if($request->has("type")) $UsersOrg->type = $request->get("type");
        $UsersOrg->save();
        echo $UsersOrg->toJson();
    }

    static function destroy($id){
        $model = UsersOrg::findOrFail($id);
        $model->delete();
        echo $model->toJson();
    }
}