<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Core\Commands;

use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Boot\System\Core\Foundation\Facades\Core;
use Boot\Support\Facades\File;
use Symfony\Component\Console\Output\ConsoleOutput;

class ListTasksCommand extends ConsoleCommand
{
    /**
     * Имя и опции консольной команды.
     *
     * @var string
     */
    protected $signature = "core:list:tasks {--withfilename}";

    /**
     * Описание команды
     *    
     * @var string    
     */
    protected $description = "Вывод всех Tasks в приложении";

    /**
     * Конструктор команды
     *
     * @param  Symfony\Component\Console\Output\ConsoleOutput $console
     * @return void
     */
    public function __construct(ConsoleOutput $console)
    {
        parent::__construct();

        $this->console = $console;
    }

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        foreach (Core::getSectionNames() as $sectionName) {
            $this->console->writeln("<fg=yellow> [$sectionName]</fg=yellow>");
            $directory = modules_path($sectionName . '/Tasks');

            if (File::isDirectory($directory)) {
                $files = File::allFiles($directory);

                foreach ($files as $action) {
                    $fileName = $originalFileName = $action->getFilename();

                    $fileName = str_replace(array('Task.php', '.php'), '', $fileName);
                    $fileName = uncamelize($fileName);

                    $includeFileName = '';
                    if ($this->option('withfilename')) {
                        $includeFileName = "<fg=red>($originalFileName)</fg=red>";
                    }

                    $this->console->writeln("<fg=green>  - $fileName</fg=green>  $includeFileName");
                }
            }
        }
    }
}
