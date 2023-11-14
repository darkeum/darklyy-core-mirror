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

use Boot\Support\Numb;
use Catalog_v2\Model\Cert;
use Catalog_v2\Model\Pump;
use Boot\System\Database\DB;
use Catalog_v2\Model\Catalog;
use Boot\Support\Facades\Storage;
use Boot\System\Image\Facades\Image;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PumpRevers extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'project:pump:revers';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Разворот графиков';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->withProgressBar(Pump::where('type', 'QH')->get(), function ($pump) {
            // $max_x = 0;
            // $max_y = 0;
            // $min_y = 9999;
            // $min_x = 9999;
            foreach ($pump->data as $k => $v) {
                $v = (array) $v;
                if ($v['Q'] != '') {
                    $q = Numb::prepareNumeric($v['Q']);
                }
                $h = Numb::prepareNumeric($v['H']);
            }
            if ($q == 0) {
               $ppp = Pump::find($pump->id);
               $good = array_reverse($ppp->data);
               $ppp->update(['data' => $good]);
            //    dd($pump->id);
            }
        });
    }
}
