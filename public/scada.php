<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Websocket class
*/

//подключаем основные константы 
require dirname(__FILE__) . '/../define.php';
header('Access-Control-Allow-Origin: *');
//маркер чистой загрузки без роутинга и шаблонизатора
$coreOnly = true;
//подключаем и инициализируем загрузчик
require ROOT . 'lib/loader.php';

use \Project\Model\Scales;
use \Scada\Scada;
use ModbusTcpClient\Composer\Read\ReadRegistersBuilder;
use ModbusTcpClient\Composer\Read\Register\ReadRegisterRequest;
use ModbusTcpClient\Utils\Packet;
use ModbusTcpClient\Packet\RtuConverter;
$registers = [
    ['uri' => '194.37.254.51:40596', 'unitId' => '1', 'type' => 'float', 'address' => 2, 'name' => 'test1'],
    ['uri' => '194.37.254.51:40596', 'unitId' => '1', 'type' => 'float', 'address' => 4, 'name' => 'test2'],
    ['uri' => '77.94.122.24:502', 'unitId' => '1', 'type' => 'float', 'address' => 1, 'name' => 'test'],
    ['uri' => '77.94.122.24:502', 'unitId' => '1', 'type' => 'float', 'address' => 3, 'name' => 'test2'],
  
];


$fc3 = ReadRegistersBuilder::newReadInputRegisters()
->allFromArray($registers)
->build();
requestWithReactPhp($fc3);

/**
 * This will do 'parallel' socket request with help of ReactPHP socket library (https://github.com/reactphp/socket)
 * Install dependency with 'composer require react/socket:^1.6'
 *
 * NB: install PHP extension ('ev', 'event' or 'uv') if the concurrent socket connections are more than 1024.
 *
 * @param ReadRegisterRequest[] $requests
 */
function requestWithReactPhp(array $requests)
{
    $logger = new EchoLogger();
    $loop = React\EventLoop\Factory::create();

    $promises = [];
    foreach ($requests as $request) {
        $promise = new React\Promise\Deferred();
        $promises[] = $promise->promise();

        $connector = new React\Socket\Connector($loop, array(
            'dns' => false,
            'timeout' => 0.2
        ));
        // dump('start');
        $connector->connect($request->getUri())->then(
            function (React\Socket\ConnectionInterface $connection) use ($request, $promise, $logger) {
               
                if($request->getUri() != '77.94.122.24:502'){
                    $rtuPacket = RtuConverter::toRtu($request->getRequest());
                    $connection->write($rtuPacket);
                } else {
                    $connection->write($request);
                }
                $connection->on('data', function ($data) use ($connection, $promise, $request, $logger) {
                    $receivedData = b'';
                    $receivedData .= $data;
                    if($request->getUri() != '77.94.122.24:502'){                   
                        $response = RtuConverter::fromRtu($receivedData); 
                       
                    } else {
                        $response = $data;
                        // sleep(2); 
                    }
                    dump($request->parse($response));
                    $promise->resolve($request->parse($response));

                   $connection->end();
                    
                });
                $connection->on('error', function ($data) use ($connection, $promise, $logger) {
                    $logger->debug("error data: " . unpack('H*', $data)[1]);

                    $promise->reject('Request failed: ' . print_r($data, true));
                    $connection->end();
                    // dd('dddd');
                });
            },
            function (Exception $error) use ($promise) {
                $promise->reject('could not connect to uri: ' . $error->getMessage());
                // dd('dddd');
            });
    }

    // $timer = $loop->addPeriodicTimer(0.1, function () {
    //     echo 'tick!' . PHP_EOL;
    // });

    React\Promise\all($promises)->then(
        function ($values) {
            // echo 'All resolved:' . PHP_EOL;
            // var_dump($values);
        },
        function ($reason) {
            // echo 'Rejected:' . PHP_EOL;
            // var_dump($reason);
        }
    )->always(function () use ($loop) {
       $loop->stop();
    });

    $loop->run();
}

