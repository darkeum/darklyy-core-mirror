<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\UI\CLI\Commands;

use Boot\Support\Numb;
use Catalog_v2\Model\Cert;
use Catalog_v2\Model\Pump;
use Boot\System\Database\DB;
use Catalog_v2\Model\Catalog;
use Boot\Support\Facades\Storage;
use Boot\System\Image\Facades\Image;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Storage\Model\StorageDetail;

class PumpLimits extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'developers:storage:copy';

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
        $variable = StorageDetail::where('storage_id', 1999)->get()->toArray();
      foreach ($variable as $key => $value) {
        unset($value['id']);
        $value['storage_id']= 2001;
        $value['count_now'] = $value['count_now'] * -1;
        StorageDetail::create($value);
      }
    }
}
