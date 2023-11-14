<?php

namespace Catalog_v2\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PumpPointsCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'pump:points';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Взятие точек насосов';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        \Catalog_v2\Pump::points($this);
    }
}
