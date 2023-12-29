<?php

# Фильтрация

$cols = $this->default['cols'];
$t = $c['type'] ?? $cols['type'];

# Фильтрация - Функция
$filter = function ($t, $f, $def = null) {
    return array_key_exists($t, $f) ? $f[$t] : $def;
};

# Что фильтруем?
$filters = [
    'label', 'format_date',
    //'render',
    'manual', 'encode', 'sortable', 'search', 'search_op', 'width', 'className', 'icon', 'priority'
];

foreach ($filters as $fi) {
    if (!isset($c[$fi])) {
        $x = $cols[$fi];
        $c[$fi] = $filter($t, $x, current($x));

        //if ($fi == 'width') {
        //    //dd($x);
        //    dd(key($x));
        //}

    }
}

####################################################################

#label -
//if (!isset($c['label'])) {
//    $f = $cols['label'];
//    //$c['label'] = array_key_exists($t, $f) ? $f[$t] : current($f);
//    $c['label'] = $filter($t, $f, current($f));
//}

#format_date - Формат даты
//if (!isset($c['format_date'])) {
//    $f = $cols['format_date'];
//    $c['format_date'] = $filter($t, $f);
//}

#render - Фильтрация по типу поля для render
//if (isset($c['render']) or !isset($c['render'])) {
if (!isset($c['render'])) {
    $f = $cols['render'];
    $tt = $c['render'] ?? ''; // Исправить !!!
    $c['render'] = $filter($tt, $f, current($f));
}

#manual - Фильтр по типу поля для manual по умолчанию
//if (!isset($c['manual'])) {
//    $f = $cols['manual'];
//    $c['manual'] = $filter($t, $f, current($f));
//}

#encode - Фильтр по типу поля для encode по умолчанию
//if (!isset($c['encode'])) {
//    $f = $cols['encode'];
//    $c['encode'] = $filter($t, $f, current($f));
//}

#sortable - Фильтр по типу поля для sortable по умолчанию
//if (!isset($c['sortable'])) {
//    $f = $cols['sortable'];
//    $c['sortable'] = $filter($t, $f, current($f));
//}

#search - Фильтр по типу поля для search по умолчанию
//if (!isset($c['search'])) {
//    $f = $cols['search'];
//    $c['search'] = $filter($t, $f, current($f));
//}

#search_op - Фильтр по типу поля для search_op по умолчанию
//if (!isset($c['search_op'])) {
//    $f = $cols['search_op'];
//    $c['search_op'] = $filter($t, $f, current($f));
//}

#width - Фильтр по типу поля для width по умолчанию
// if (!isset($c['width'])) {
//     // $f = $cols['width'];
//     // $c['width'] = $filter($t, $f, current($f));
// }
// $c['width'] = is_int($c['width']) ? $c['width'] . '%' : $c['width'];

#className - Фильтр по типу поля для className по умолчанию
//if (!isset($c['className'])) {
//    $f = $cols['className'];
//    $c['className'] = $filter($t, $f, current($f));
//}

#icon - Иконка вместо заголовка столбца
//if (!isset($c['icon'])) {
//    $f = $cols['icon'];
//    $c['icon'] = $filter($t, $f);
//}

#priority - Приоритет показа столбцов
//if (!isset($c['priority'])) {
//    $f = $cols['priority'];
//    $c['priority'] = $filter($t, $f);
//}

####################################################################
# Дополнительный custom функционал

#popover - Временный фильтр для true popover
if (isset($c['popover']) and $c['popover'] == true) {
    if (is_bool($c['popover']) === true) {
        $c['popover'] = $cols['popover'];
    }
}

#excel_only - Экспорт
if (isset($c['excel_only']) && $c['excel_only'] == true) {
    $c['visible'] = false;
}

//$c['configs'] = json_encode($configs, JSON_UNESCAPED_UNICODE);