<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace  Boot\Abstracts\Jobs;

use Boot\System\Core\Abstracts\Jobs\Job as AbstractJob;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Job extends AbstractJob implements ShouldQueue
{
}
