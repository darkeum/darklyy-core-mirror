<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\App\Console;

use Boot\System\Console\Scheduling\Schedule;
use Boot\System\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Определение расписания команд приложения.
     *
     * @param  \Boot\System\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('scada:work')->everyMinute()->runInBackground();
        $schedule->command('scada:crash')->everyMinute()->runInBackground();
        $schedule->command('price:currency')->daily()->runInBackground();
        $schedule->command('price:work')->twiceDaily(0, 6)->runInBackground();
        $schedule->command('calls:get')->everyMinute()->runInBackground();
        $schedule->command('report:supply:get')->daily()->runInBackground();
    }

    /**
     * Регистрация команд для приложения.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
