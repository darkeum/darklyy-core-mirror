<?php

namespace Chat\Controller;

use \Boot\Controller\AbstractController;
use Boot\System\Http\Request;
use \Boot\Support\Facades\Auth;

class ChatMessageController extends AbstractController{
    static function index(){
        // $grid = new IndexGrid;
        // display('@/manager/index.html.twig', [
        //     'grid' =>  $grid->render(),
        // ]);
    }

    static function create(){

    }

    static function store(Request $request){
        $chat_message = \Chat\Model\ChatMessage::create($request->request->all());

        static::send_socket($chat_message->chat_id, [$chat_message->toArray()]);

        echo $chat_message->toJson();
    }

    static function show($id){
       
    }

    static function edit($id){
        
    }

    static function update(Request $request, $id){
        
    }

    static function destroy($id){
        
    }

    static function load_file(Request $request){
        $chat_id = $request->request->chat_id;
        // $files = $request->files

        // static::send_socket($chat_message->chat_id, [$chat_message->toArray()]);
    }

    static function send_socket($chat_id, $message){
        try {
            $instance = stream_socket_client('tcp://atri-energo.ru:1234', $errno, $errstr, 30);
            if($instance){
                $user_list = [];
                $users = \Chat\Model\ChatUser::where("chat_id", $chat_id)->get();
                foreach($users as $user){
                    if($user->uid != Auth::id()) $user_list[] = $user->uid;
                }
                fwrite($instance, json_encode(['message_type' => 'chat', 'user' => $user_list, 'message' => $message])  . "\n");
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}