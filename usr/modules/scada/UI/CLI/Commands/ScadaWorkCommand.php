<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\CLI\Commands;

use Scada\Models\Scada;
use Scada\Jobs\ScadaGetJob;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class ScadaWorkCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'scada:work';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Постановка задач для опроса объекта';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $scada = Scada::where('status', 1)->get();
        foreach ($scada as $key => $value) {
            dump($value->id);
            dispatch((new ScadaGetJob($value->id))->onQueue('scada_queue'));
        }
       
    }
}
