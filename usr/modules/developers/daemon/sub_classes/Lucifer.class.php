<?php

namespace Daemon;

class Lucifer{
    static public function start(){
        $daemons = \Daemon\Model\Daemon::where("autostart", 1);
        if($daemons->exists()){
            foreach($daemons->get() as $daemon){
                $process = new \Daemon\DaemonProcess;
                $process->setCommand($daemon->command);
                if(empty($daemon->pid) || !$process->status()){
                    $process->runCom();
                    $processId = $process->getPid();
                    $daemon->update(['pid'=>$processId]);
                }
            }
        }
    }
}