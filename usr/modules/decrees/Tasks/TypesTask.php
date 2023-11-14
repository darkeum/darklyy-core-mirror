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

use Boot\Abstracts\Tasks\Task;
use Decrees\Exceptions\TypesException;

class TypesTask extends Task
{    
    /**
     * Получение типов приказов, если указан id то выдает конкретный тип приказа
     *
     * @param  int|null $id
     * @param  string $type Тип вывода (default: название, prefix: префикс, icon: иконки)
     * @return array|string
     * @throws Decrees\Exceptions\TypesException Если указан не существующий тип
     */
    public function run(int $id = null, $type = "default")
    {     
        $rows = [];   
        $rows['default'] = [
            1 => __('decrees::admin.type.activity'),
            2 => __('decrees::admin.type.staff'),
            3 => __('decrees::admin.type.trip'),
        ];
        $rows['prefix'] = [1 => "ОД", "ЛС", "К"];
        $rows['icon'] = [
            1 => '<span class="far fa-industry text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $rows['default'][1] . '"></span>',
            2 => '<span class="far fa-users text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $rows['default'][2] . '"></span>',
            3 => '<span class="far fa-plane text-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $rows['default'][3] . '"></span>',
        ];

        if(empty($id)) {
            if(isset($rows[$type])){
                return $rows[$type];
            } else {
                throw new TypesException();
            }           
        } else {
            if(isset($rows[$type][$id])) {
                return $rows[$type][$id];
            } else {
                throw new TypesException();
            }
        }
    }

   
}
