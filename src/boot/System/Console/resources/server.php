<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Этот файл позволяет нам эмулировать функциональность Apache «mod_rewrite» из
// встроенный веб-сервер PHP. Это обеспечивает удобный способ тестирования Darklyy.
// приложение без установки «настоящего» программного обеспечения веб-сервера.
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

require_once $publicPath . '/index.php';
