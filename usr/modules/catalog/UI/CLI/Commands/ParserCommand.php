<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class ParserCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'parser';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Parser';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
       \Catalog\Stock\Provider\Ebara::parse();
    }
}
