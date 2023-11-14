<?php
ini_set("display_errors", true);
date_default_timezone_set("Europe/Moscow");
define("DB_LOCAL", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_BASE", "atry_constructor");
define("THEME_CONSTRUCT", "/usr/modules/selection/scheme/");

function handleException($exception) {
  echo "Sorry, a problem occurred. Please try later.";
  error_log($exception->getMessage());
}

set_exception_handler('handleException');
?>
