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
use Decrees\Models\Command;

class CommandsTransfer extends ConsoleCommand
{
    protected $signature = 'decrees:transfer';

    protected $description = 'Служебная команда для переноса приказов из старой таблицы';

    protected $hidden = true;

    public function handle()
    {
        $commands = Command::all();
        foreach ($commands as $key => $value) {
            if (empty(Decrees::find($value->id))) {
                Decrees::create(
                    [
                        'id' =>  $value->id,
                        'deleted_at' => null,
                        'numb' => $value->numb,
                        'numb_counter' => $value->numb_counter,
                        'uid' => $value->uid,
                        'org' => $value->org,
                        'comment' => $value->comment,
                        'started_at' => $value->date,
                        'ended_at' => $value->date_end,
                        'type' => $value->type,
                        'file_id' => $value->file,
                        'scan_id' => $value->scan,
                        'worker' => $value->worker,
                        'data' => $value->data,
                        'status' => 1,
                        'created_at' => $value->date_create,
                        'updated_at' => null,
                    ]
                );
            }
        }
    }
}
