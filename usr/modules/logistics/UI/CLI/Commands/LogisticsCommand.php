<?php

namespace Logistics\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class LogisticsCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'logistics:get';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Логистика';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        while(1) {
            try{
                $LogisticsDaemon = new \Logistics\LogisticsDaemon;
                $LogisticsDaemon->start();
                sleep(3600);
            } catch(\Exception $e){
                setLog('Error', '0', 'daemon', $e->getMessage());
                dump($e->getMessage());
            }
        }
    }
}
