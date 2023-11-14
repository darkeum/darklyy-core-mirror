<?php

namespace Boot\System\Console\Foundation;

use RuntimeException;
use Workerman\Worker;
use Boot\Support\Facades\Auth;
use Boot\System\Console\Command;
use Boot\System\Filesystem\Filesystem;
use Boot\System\Sanctum\PersonalAccessToken;

class WebSocketCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'websocket:start';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'websocket:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Активация WebSocket';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;


    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function handle()
    {        //список клиентов
        $client = [];
        global $argv;

        $argv[1] = 'start';
        // создаём ws-сервер, к которому будут подключаться все наши пользователи
        $ws_worker = new Worker("websocket://atri-energo.ru:4444");
        $ws_worker->name = 'pusher';
        // создаём обработчик, который будет выполняться при запуске ws-сервера
        $ws_worker->onWorkerStart = function () use (&$users, &$task, &$client) {
            // создаём локальный tcp-сервер, чтобы отправлять на него сообщения
            $inner_tcp_worker = new Worker("tcp://atri-energo.ru:1234");
            $inner_tcp_worker->name = 'publisher';
            // создаём обработчик сообщений, который будет срабатывать,
            // когда на локальный tcp-сокет приходит сообщение
            $inner_tcp_worker->onMessage = function ($connection, $data) use (&$client) {

                $data = json_decode($data, true);
                // dump($data);
                if (isset($data['message_type'])) {
                    $message_type = $data['message_type'];
                    switch ($message_type) {
                        case 'scada':
                            if (isset($client[$message_type]) && isset($client[$message_type][$data['scada_id']])) {
                                foreach ($client[$message_type][$data['scada_id']] as $users) {
                                    foreach ($users as $wc) {
                                        $wc->send(json_encode($data['message'], JSON_UNESCAPED_UNICODE));
                                    }
                                }
                            }
                            break;

                        case 'chat':
                            if (isset($data['message']) && isset($client[$message_type]) && isset($data['user'])) {
                                $message = $data['message'];
                                foreach ($data['user'] as $value) {
                                    if (isset($client[$message_type][$value])) {
                                        foreach ($client[$message_type][$value] as $conn) {
                                            $conn->send($message);
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
            };
            $inner_tcp_worker->listen();
        };

        $ws_worker->onConnect = function ($connection) use (&$client) {
            $connection->onWebSocketConnect = function ($connection) use (&$client) {           
                if (isset($_GET['token']) && !empty($_GET['token'])) {
                    $user = PersonalAccessToken::findToken($_GET['token']);
                    if (!empty($user)) {
                        Auth::loginUsingId($user->tokenable_id, true);                    
                        if (isset($_GET['wss_type'])) {
                            $wss_type = $_GET['wss_type'];
                            switch ($wss_type) {
                                case 'scada':
                                    $scada_id = $_GET['scada_id'];
                                    $client[$wss_type][$scada_id][$user->tokenable_id][] = $connection;
                                    break;

                                case 'chat':
                                    $client[$wss_type][$user->tokenable_id][] = $connection;
                                    break;
                            }
                        }
                    } else {
                        $check = false;
                    }
                } else {
                    $client['guest']['connections'][] = $connection;
                }
            };
        };

        $ws_worker->onClose = function ($connection) use (&$client) {
            foreach ($client as $key => $value) {
                switch ($key) {
                    case 'scada':
                        foreach ($value as $key_type => $value_type) {
                            foreach ($value_type as $key_user => $value_user) {
                                $conn = array_search($connection, $value_user);
                                if ($conn !== false) {
                                    unset($client[$key][$key_type][$key_user][$conn]);
                                    if (empty($client[$key][$key_type][$key_user])) {
                                        unset($client[$key][$key_type][$key_user]);
                                    }
                                    if (empty($client[$key][$key_type])) {
                                        unset($client[$key][$key_type]);
                                    }
                                }
                            }
                        }
                        break;
                    case "chat":
                        foreach ($value as $key_user => $value_user) {
                            $conn = array_search($connection, $value_user);
                            if ($conn !== false) {
                                unset($client[$key][$key_user][$conn]);
                                if (empty($client[$key][$key_user])) {
                                    unset($client[$key][$key_user]);
                                }
                            }
                        }

                        break;
                }
            }
        };
        
        Worker::runAll();
    }
}
