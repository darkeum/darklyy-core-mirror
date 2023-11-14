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
use Support\Exceptions\TypesException;

class TypesTask extends Task
{
    /**
     * Получение типов заявок, если указан id то выдает конкретный тип заявки
     *
     * @param  string|null $id
     * @param  string $type Тип вывода (default: название, prefix: префикс, icon: иконки)
     * @return array|string
     * @throws Decrees\Exceptions\TypesException Если указан не существующий тип
     */
    public function run(string|null $id = null, $type = "default")
    {
        $rows = [];
        $rows['default'] = [
            'site'          => __('support::types.site'),
            'portal'        => __('support::types.portal'),
            'lk'            => __('support::types.lk'),
            'office'        => __('support::types.office'),
            'price'         => __('support::types.price'),
            'catalog'       => __('support::types.catalog'),
            'calculation'   => __('support::types.calculation'),
            'other'         => __('support::types.other'),
        ];
        $rows['icon'] = [
            'site'          => '<span class="fa-light fa-globe text-warning" title="' . $rows['default']['site'] . '"></span>',
            'portal'        => '<span class="fa-light fa-gears text-primary" title="' .    $rows['default']['portal'] . '"></span>',
            'lk'            => '<span class="fa-light fa-users text-success" title="' .    $rows['default']['lk'] . '"></span>',
            'office'        => '<span class="fa-light fa-desktop text-danger" title="' .    $rows['default']['office'] . '"></span>',
            'price'         => '<span class="fa-light fa-ruble-sign text-system" title="' .    $rows['default']['price'] . '"></span>',
            'catalog'       => '<span class="fa-light fa-sitemap text-info" title="' .    $rows['default']['catalog'] . '"></span>',
            'calculation'   => '<span class="fa-light fa-calculator text-success" title="' .    $rows['default']['calculation'] . '"></span>',
            'other'         => '<span class="fa-light fa-circle-question text-success" title="' .    $rows['default']['other'] . '"></span>',

        ];
        $rows['icon_big'] = [
            'site'          => '<i class="fa-light fa-globe font-large-1 mb-75"></i>',
            'portal'        => '<i class="fa-light fa-gears font-large-1 mb-75"></i>',
            'lk'            => '<i class="fa-light fa-users font-large-1 mb-75"></i>',
            'office'        => '<i class="fa-light fa-desktop font-large-1 mb-75"></i>',
            'price'         => '<i class="fa-light fa-ruble-sign font-large-1 mb-75"></i>',
            'catalog'       => '<i class="fa-light fa-sitemap font-large-1 mb-75"></i>',
            'calculation'   => '<i class="fa-light fa-calculator font-large-1 mb-75"></i>',
            'other'         => '<i class="fa-light fa-circle-question font-large-1 mb-75"></i>',
        ];
        $rows['description'] = [
            'site'          => __('support::types.site.description'),
            'portal'        => __('support::types.portal.description'),
            'lk'            => __('support::types.lk.description'),
            'office'        => __('support::types.office.description'),
            'price'         => __('support::types.price.description'),
            'catalog'       => __('support::types.catalog.description'),
            'calculation'   => __('support::types.calculation.description'),
            'other'         => __('support::types.other.description'),

        ];
        $rows['all'] = [];
        foreach ($rows['default'] as $key => $value) {
            $rows['all'][$key] = [
                'default' => $value,
                'icon' => $rows['icon'][$key],
                'icon_big' => $rows['icon_big'][$key],
                'description' => $rows['description'][$key],
            ];
        }
        if (empty($id)) {
            if (isset($rows[$type])) {
                return $rows[$type];
            } else {
                throw new TypesException();
            }
        } else {
            if (isset($rows[$type][$id])) {
                return $rows[$type][$id];
            } else {
                throw new TypesException();
            }
        }
    }
}
