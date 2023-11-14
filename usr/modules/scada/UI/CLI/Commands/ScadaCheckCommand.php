<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\CLI\Commands;

use Boot\Models\Users;
use Scada\Models\Scada;
use Boot\Support\Carbon;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\Notification;
use Scada\Notifications\ErrorConnection;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class ScadaCheckCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'scada:check';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Проверка доступности объектов диспетчеризации';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        // $scada = Scada::where('status', 1)->get();
        // foreach ($scada as $value) {
        //     if ($value->read_at <= Carbon::now()->subMinutes(5)) {
        //         $users = Users::whereIn('id', Config::get('scada.admin'))->get();
        //         Notification::send( $users , new ErrorConnection( $value ));
        //     }
        // }
    }
}
