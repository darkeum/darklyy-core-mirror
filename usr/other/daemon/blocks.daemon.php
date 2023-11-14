<?php

require 'require.daemon.php';
use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

$key = \Selection\Model\SelectionRequest::$key;
\Selection\Model\SelectionRequest::key_create();

$ansi = new \Bramus\Ansi\Ansi();



$ansi->color(array(SGR::COLOR_FG_GREEN, SGR::COLOR_BG_BLACK));
$ansi->text('Осуществлен запуск демона для блоков')->lf();
$ansi->text('Ожидаю задание...')->lf();


while(1) {
    try{
        if(file_exists($key)){
            unlink($key);
            $data = \Selection\Model\SelectionRequest::where("daemon", 0)->get()->toArray();
            if(count($data) > 0){
                foreach($data as $item){
                    $id = $item["id"];
                    $ansi->text('Получено задание на подсчет блока'.$id)->lf();
                    $ansi->text('Запускаю расчет')->lf();                    
                    $bc = new \Calculation\BlockCalculation();
                    $ansi->text('Заполняю данные из базы данных')->lf();      
                    $bc->getRequsetDataFromSQL($id);
                    $ansi->text('Выполняю расчет блоков')->lf();   
                    $bc->calculationBlocks();
                    $ansi->text('Сохраняю полученный результат')->lf();   
                    $bc->saveResultData();
                }
            }
            $ansi->text('Все задания выполнены')->lf();
            $ansi->text('Ожидаю задание...')->lf();
        }
    } catch(\Exception $e){
        dump($e->getMessage());
        setLog('Error', '0', 'daemon', $e->getMessage());
    }
}