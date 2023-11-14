<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\UI\CLI\Commands;

use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Project\Actions\Calculation\Pump\Parse\WiloParseAction;

class PumpParseWiloCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'project:pump:parse:wilo';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Получение информации о насосах WILO';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $value = 'TOP-S';
        $pumpParse = new WiloParseAction();
        $pumpParse->initClient();
        $pumpParse->initAuth();
        $pumpParse->getPumpSeries($value);
        $pumpParse->getPumps();
    }
}
