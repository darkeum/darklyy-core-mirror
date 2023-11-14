<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      ZeoNish
*/

namespace Calls\UI\CLI\Commands;

use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Boot\Models\Users;
use Boot\Support\Facades\DB;

class CallsPhoneFixCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'calls:phone:fix';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Исправления телефонов пользователей сайта';

    /**
     * Обработчик консольной команды.
     *
     */
    public function handle()
    {
        $users = Users::all();

        foreach ($users as $user) {

            $phone = $user->phone;

            if (strlen($phone) < 5) {

                $phone = '';

            } else {

                if ($phone) {
                    $phone = str_replace(' ', '', $phone);
                    $phone = str_replace('-', '', $phone);
                    $phone = str_replace('(', '', $phone);
                    $phone = str_replace(')', '', $phone);

                    $phone = str_replace('+7', '', $phone);

                    if (strlen($phone) == 11) {
                        $phone = substr($phone, 1);
                    }

                    $phone = '+7' . $phone;

                    DB::table('users')
                        ->where('id', '=', $user->id)
                        ->update(['phone' => $phone]);
                }

            }

            echo $phone;
            echo PHP_EOL;

        }

        echo PHP_EOL; // Перенос строки в консоли
        echo 'Телефоны исправлены';
    }
}
