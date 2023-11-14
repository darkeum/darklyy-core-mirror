<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sync\UI\CLI\Commands;

use Sync\Models\SyncBase;
use Sync\Actions\SyncAction;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class SyncCommand extends ConsoleCommand
{  
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'sync:start
                            {base? : Идентификатор базы программы для синхронизации}
                            {--force : Принудительная работа с игнорированием ключевого файла}
                            {--once : Выполнить команду однократно}
                            {--host= : Имя хоста}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Синхронизировать данные со внешними программами';
    
    public $commandOutput;
    public $sectionError;

    protected function getBase($base)
    {
        if(!empty($base)){
            if(!empty($baseReturn = SyncBase::find($base))){
                return [$baseReturn];
            } else {
                $this->error('Не найдена указанная программа для синхронизации');
            }
        } else {
            if(!empty($baseReturn = SyncBase::where('status', 1)->get())){
                return $baseReturn;
            }   
            else {
                $this->error('Отсутствуют программы для синхронизации');
            }
        }
    }


    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->commandOutput = $this->output->getOutput();
        $this->sectionError = $this->commandOutput->section();
        $this->info('Начало синхронизации');
        $base = $this->getBase($this->input->getArgument('base'));
        $this->host = $this->input->getOption('host');
        $sd = new \Sync\Sync;
        $key = $sd->get_key();
        $next = true;
        while ($next) {
            if (file_exists($key) || $this->option('force')) {
                foreach ($base as $key => $value) {
                    app(SyncAction::class)->run($this, $value);
                }
            }
            if($this->option('once')){
                $next = false;
            }
        }
    }
}
