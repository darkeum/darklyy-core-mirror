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

use Scada\Models\ScadaTags;
use Boot\Abstracts\Tasks\Task;

class ScadaTagsTask extends Task
{
  public function run(array $data): array
  {
    $result = [];
    $tags = ScadaTags::where('scada_id', $data['id'])->where('status', 1)->get()->toArray();
    foreach ($tags as $v) {
      $result['tags'][] = $v;
    }
    return $result;
  }
}
