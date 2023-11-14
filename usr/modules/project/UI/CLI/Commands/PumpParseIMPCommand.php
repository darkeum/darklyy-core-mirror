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
use Project\Actions\Calculation\Pump\Parse\IMPParseAction;

class PumpParseIMPCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'project:pump:parse:imp   
                            {--images : Получение изображений}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Получение информации о насосах IMP';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $images = $this->input->getOption('images');
        app(IMPParseAction::class)->run($images);
    }
}
