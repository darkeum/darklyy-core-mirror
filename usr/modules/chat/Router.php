<?php

use \Boot\Support\Facades\Router;
use \Boot\Services\Request;
use Chat\Controller\ChatController;
use Chat\Controller\ChatUserController;
use Chat\Controller\ChatMessageController;

Router::mount('/administration/module/chat', function (){ 
    // Router::get('/edit-contracts-list/{id}',  [OrganizationsController::class, "editContractsList"])->name("organizations.editContractsList");

    Router::post('/load_file',  [ChatMessageController::class, "load_file"])->name("chat_message.load_file");
    Router::resource('/message',  ChatMessageController::class)->names("chat_message");
    Router::get('/messages/{id}',  [ChatController::class, "messages"])->name("chat.messages");
    Router::get('/list',  [ChatController::class, "list"])->name("chat.list");
    // Router::get("/pp", function(){
    //     dump("!!!!");
    //     foreach(\Chat\Model\Chat::where("uid", "LIKE", '%"33",%')->get() as $item){
    //         \Chat\Model\ChatUser::create(["chat_id" => $item->id, "uid" => 33]);
    //     }
    // });
    Router::resource('/',  ChatController::class)->names("chat");
}, "admin.");

// dd(Router::dumpAlias());