<?php

# Фильтрация

$cols = $this->default['cols'];
$t = $c['type'] ?? $cols['type'];

# Фильтрация - Функция
$filter = function ($t, $f, $def = null) {
    return array_key_exists($t, $f) ? $f[$t] : $def;
};

# Что фильтруем?
$filters = $this->default['filters'];

foreach ($filters as $fi) {
    if (!isset($c[$fi])) {
        $x = $cols[$fi];
        $c[$fi] = $filter($t, $x, current($x));
    }
}
####################################################################

#render - Фильтрация по типу поля для render
//if (isset($c['render']) or !isset($c['render'])) {
if (!isset($c['render'])) {
    $f = $cols['render'];
    $tt = $c['render'] ?? ''; // Исправить !!!
    $c['render'] = $filter($tt, $f, current($f));
}
####################################################################

#width - Фильтр по типу поля для width по умолчанию
$c['width'] = is_int($c['width']) ? $c['width'] . '%' : $c['width'];
####################################################################

#icon - Иконка вместо заголовка столбца
if (isset($c['icon'])) {
   $f = $cols['icon'];
   $c['icon'] = $filter($t, $f);
}
####################################################################

####################################################################
# Дополнительный custom функционал

#popover - Временный фильтр для true popover
if (isset($c['popover']) and $c['popover'] == true) {
    if (is_bool($c['popover']) === true) {
        $c['popover'] = $cols['popover'];
    }
}
####################################################################

#excel_only - Экспорт
if (isset($c['excel_only']) && $c['excel_only'] == true) {
    $c['visible'] = false;
}
####################################################################

//$c['configs'] = json_encode($configs, JSON_UNESCAPED_UNICODE);
####################################################################