<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (09.11.2023 22:34)
*/

namespace Calls\UI\CLI\Commands;

use Boot\Abstracts\Commands\ConsoleCommand;
use Calls\Models\Calls;

class CallsClearTableCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'calls:clear_table';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Очистка таблицы JMY_calls';

    public function handle() {
        echo 'Начали - ' . date('d:m:Y H:i:s', time()) . ' ';

        $calls_missing = Calls::where('file', 'missing')->get();
        foreach ($calls_missing as $call_m) {
            Calls::find($call_m->id)->update([
                'file' => null
            ]);
        }

        echo PHP_EOL; // Перенос строки в консоли
        echo 'Закончили - ' . date('d:m:Y H:i:s', time());
    }
}