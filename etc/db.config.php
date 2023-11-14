<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


$dbhost = env('DB_HOST', '127.0.0.1');
$dbuser = env('DB_USERNAME', 'forge');
$dbpass = env('DB_PASSWORD', '');
$dbname = env('DB_DATABASE', 'forge');
$dbport = env('DB_PORT', '3306');

$prefix = $user_prefix = 'JMY';
$user_db = env('DB_DATABASE', 'forge');

