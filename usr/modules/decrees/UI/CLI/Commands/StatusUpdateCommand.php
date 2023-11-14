<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\UI\CLI\Commands;

use Decrees\Models\Decrees;
use Boot\Abstracts\Commands\ConsoleCommand;
use Boot\Support\Carbon;

class StatusUpdateCommand extends ConsoleCommand
{
    protected $signature = 'decrees:status';

    protected $description = 'Служебная команда обновления статуса по истечении срока действия приказа';

    protected $hidden = true;

    public function handle()
    {
        Decrees::where('status', 1)->whereNotNull('ended_at')->where('ended_at', '<', Carbon::now())->update(['status' => 0]);
    }
}