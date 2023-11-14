<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Tasks;

use Boot\Abstracts\Tasks\Task;

class ScadaActionTask extends Task
{
  public function run(array $data): array
  {
    return \Scada\Models\ScadaActions::create($data)->toArray();
  }
}
