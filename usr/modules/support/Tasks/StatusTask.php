<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\Tasks;

use Boot\Abstracts\Tasks\Task;
use Support\Exceptions\StatusException;

class StatusTask extends Task
{    
    /**
     * Получение статуса заявок, если указан id то выдает конкретный статус заявки
     *
     * @param  int|null $id
     * @param  string $type Тип вывода (default: название, prefix: префикс, badge: иконки)
     * @return array|string
     * @throws Decrees\Exceptions\StatusException Если указан не существующий статус
     */
    public function run(int $id = null, $type = "default")
    {     
        $rows = [];

        // По умолчанию
        $rows['default'] = [
            '1'   => __('support::status.wait'),
            '2'   => __('support::status.active'),
            '3'   => __('support::status.complete'),
            '4'   => __('support::status.cancel'),
        ];

        // Badge
        $rows['badge'] = [
            '1'   => '<span class="badge rounded-pill badge-light-warning d-block">' .    $rows['default'][1] . '</span>',
            '2'   => '<span class="badge rounded-pill badge-light-info d-block">' .    $rows['default'][2] . '</span>',
            '3'   => '<span class="badge rounded-pill badge-light-success d-block">' .    $rows['default'][3] . '</span>',
            '4'   => '<span class="badge rounded-pill badge-light-danger d-block">' .    $rows['default'][4] . '</span>',
        ];

        if(empty($id)) {
            if(isset($rows[$type])){
                return $rows[$type];
            } else {
                throw new StatusException();
            }           
        } else {
            if(isset($rows[$type][$id])) {
                return $rows[$type][$id];
            } else {
                throw new StatusException();
            }
        }
    }   
}
