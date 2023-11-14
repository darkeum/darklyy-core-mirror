<?php
$lang = 'ru';
$_CONFIG = array(
    'root_path' => dirname(__FILE__) . '/../php/',

    'encoding' => 'utf-8',

    'pdo_dsn' => 'mysql:dbname=atrienergo_data;host=188.127.237.42;port=3310;',
    'pdo_user' => 'atrienergo_user',
    'pdo_pass' => 'WApOySgK',
);
header("Content-Type: text/html; charset={$_CONFIG['encoding']};");
require_once($_CONFIG['root_path'] . 'jqGridLoader.php');
$jq_loader = new jqGridLoader();
$jq_loader->set('grid_path', 'grids' . DIRECTORY_SEPARATOR);
$jq_loader->set('pdo_dsn', $_CONFIG['pdo_dsn']);
$jq_loader->set('pdo_user', $_CONFIG['pdo_user']);
$jq_loader->set('pdo_pass', $_CONFIG['pdo_pass']);
$jq_loader->set('debug_output', true);
$jq_loader->addInitQuery("SET NAMES 'utf8'");
$jq_loader->autorun();
$grid = isset($_REQUEST['render']) ? $_REQUEST['render'] : 'jqSimple';
$grid = preg_replace('#[^a-zA-Z0-9_-]#', '', $grid); 
$rendered_grid = $jq_loader->render($grid);
require 'templates/_layout.php';