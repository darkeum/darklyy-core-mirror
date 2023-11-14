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
use Support\Exceptions\MarksException;

class MarksTask extends Task
{
    /**
     * Получение приоритета заявки приказов, если указан id то выдает конкретный приоритет приказа
     *
     * @param  string|null $id
     * @param  string $type Тип вывода (default: название, prefix: префикс, icon: иконки)
     * @return array|string
     * @throws Decrees\Exceptions\TypesException Если указан не существующий приоритет
     */
    public function run(string|null $id = null, $type = "default")
    {
        $rows = [];
        // По умолчанию
        $rows['default'] = [
            0   => __('support::marks.turn'),
            1   => __('support::marks.top'),
            2   => __('support::marks.vip')
        ];

        // Иконки
        $rows['icon'] = [
            0   => '<span class="far fa-industry text-warning" title="' . $rows['default'][0] . '"></span>',
            1   => '<span class="far fa-users text-primary" title="' .    $rows['default'][1] . '"></span>',
            2   => '<span class="far fa-plane text-success" title="' .    $rows['default'][2] . '"></span>',
        ];

        // Badge
        $rows['badge'] = [
            0   => '<span class="badge rounded-pill badge-light-warning d-block">' .    $rows['default'][0] . '</span>',
            1   => '<span class="badge rounded-pill badge-light-danger d-block">' .    $rows['default'][1] . '</span>',
            2   => '<span class="badge rounded-pill badge-light-success d-block">' .    $rows['default'][2] . '</span>',
        ];

        if($id === null) {
            if(isset($rows[$type])){
                return $rows[$type];
            } else {
                throw new MarksException();
            }
        } else {
            if(isset($rows[$type][$id])) {
                return $rows[$type][$id];
            } else {
                throw new MarksException();
            }
        }
    }

}
