<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\Tasks;

use Decrees\Models\Decrees;
use Decrees\Tasks\TypesTask;
use Boot\Abstracts\Tasks\Task;
use Organizations\Model\Org;

class NumbCreateTask extends Task
{
    /**
     * Генерация номера приказа в соответствии организации типу и счетчику
     *
     * @param  Decrees $decree
     * @return Decrees
     */
    public function run(Decrees $decree): Decrees
    {
        $type = $decree->type;
        $org = $decree->org;
        $org_model = Org::find($org);
        $prefix = $org_model->prefix;
        $type_prefix = app(TypesTask::class)->run($type, 'prefix');
        $year = date("Y", strtotime($decree->started_at));

        $check = Decrees::where('type', $type)->where('org', $org)->withTrashed()->whereNotNull('numb_counter')->where('id', '!=', $decree->id)->whereYear('started_at', $year)->orderBy('id', 'DESC')->first();
        if ($check) {
            $counter = $check->numb_counter;
            if (empty($counter)) {
                $counter = 1;
            } else {
                $counter++;
            }
        } else {
            $counter = 1;
        }
        $numb = $prefix . $type_prefix . "-" . $counter;
        $decree->update([
            'numb' => $numb,
            'numb_counter' => $counter
        ]);
        return $decree;
    }
}
