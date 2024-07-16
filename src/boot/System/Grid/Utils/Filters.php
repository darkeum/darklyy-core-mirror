<?php

# ФИЛЬТРАЦИЯ

$cols = $this->default['cols'];
$t = $c['type'] ?? $cols['type'];

# Фильтрация - Функция
$FILTER = function ($t, $f, $def = null) {
    return array_key_exists($t, $f) ? $f[$t] : $def;
};

# Генерация параметра
$PARAM = function ($name, $data) {
    return $name.': '.$data.',';
};

# Что фильтруем?
$filters = $this->default['filters'];
foreach ($filters as $fi) {
    if (!isset($c[$fi])) {
        $x = $cols[$fi];
        $c[$fi] = $FILTER($t, $x, current($x));
    }
}
####################################################################

#render - Фильтрация по типу поля для render
//if (isset($c['render']) or !isset($c['render'])) {
if (!isset($c['render'])) {
    $f = $cols['render'];
    $tt = $c['render'] ?? ''; // Исправить !!!
    $c['render'] = $FILTER($tt, $f, current($f));
}
####################################################################

#width - Фильтр по типу поля для width по умолчанию
$c['width'] = is_int($c['width']) ? $c['width'].'%' : $c['width'];
####################################################################

#icon - Иконка вместо заголовка столбца
if (isset($c['icon'])) {
    $f = $cols['icon'];
    $c['icon'] = $FILTER($t, $f);
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

#options - Опции для select2
if (isset($c['options'])) {
    $c['options'] = \Boot\System\Grid\Utils\Utils::jsonEncode($c['options']);
    //$c['options'] = json_encode($c['options'], JSON_UNESCAPED_UNICODE);
}
####################################################################
#other -
if (isset($c['other'])) {
    $c['other'] = \Boot\System\Grid\Utils\Utils::jsonEncode($c['other']);
}
####################################################################

//$c['configs'] = json_encode($configs, JSON_UNESCAPED_UNICODE);
####################################################################
