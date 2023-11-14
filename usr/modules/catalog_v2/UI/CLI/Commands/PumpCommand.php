<?php

namespace Catalog_v2\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PumpCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'pump:images';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Взятие картинок насосов';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        \Catalog_v2\Pump::images($this);
    }
}
