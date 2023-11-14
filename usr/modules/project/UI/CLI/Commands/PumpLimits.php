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

class PumpLimits extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'project:pump:limits';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Генерация пределов расчета';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->withProgressBar(Pump::where('max_x', 0)->get(), function (Pump $pump) {

            $max_x = 0;
            $max_y = 0;
            $min_y = 9999;
            $min_x = 9999;
            foreach ($pump->data as $k => $v) {
                $v = (array) $v;
                $q = Numb::prepareNumeric($v['Q']);
                $h = Numb::prepareNumeric($v['H']);
                if ($q >= $max_x) {
                    $max_x = $q;
                }
                if ($h >= $max_y) {
                    $max_y = $h;
                }
                if ($q <= $min_x) {
                    $min_x = $q;
                }
                if ($h <= $min_y) {
                    $min_y = $h;
                }
            }
            Pump::find($pump->id)->update([
                'max_x' => Numb::prepareNumeric($max_x),
                'max_y' => Numb::prepareNumeric($max_y),
                'min_y' => Numb::prepareNumeric($min_y),
                'min_x' => Numb::prepareNumeric($min_x),
            ]);
            $this->performTask($pump);
        });
    }
}
