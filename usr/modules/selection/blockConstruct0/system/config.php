<?php
ini_set("display_errors", true);
date_default_timezone_set("Europe/Moscow");
define("DB_LOCAL", "localhost");
define("DB_USERNAME", "atrienergo_block");
define("DB_PASSWORD", "M0d8Q2v5");
define("DB_BASE", "atrienergo_block");
define("THEME_CONSTRUCT", "/usr/modules/selection/blockConstruct/");


function handleException($exception) {
  echo "Sorry, a problem occurred. Please try later.";
  echo $exception->getMessage();
  error_log($exception->getMessage());
}

set_exception_handler('handleException');
?>
