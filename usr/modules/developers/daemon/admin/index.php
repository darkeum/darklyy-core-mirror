<?php


if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

//*************
// Change path
//*************
$dir = ROOT.'/usr/modules/daemon/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		main();
	
	// Запуск демона и получение PID (предполагается, что pid где-то сохраняется после запуска)
	/*
$command = '/opt/php72/bin/php -f /var/www/izhclient0077/data/www/atri-energo.ru/usr/other/daemon/notif.daemon.php';
$process = new \Daemon\DaemonProcess($command);
$processId = $process->getPid();*/


// Проверка статуса демона


/*
// Остановка демона
$process = new Process();
$process->setPid($processId);
$stopped = $process->stop(); // возвращает true или false*/
        
	break;
	
	case 'start':
		// $command = '/opt/php72/bin/php -f /var/www/izhclient0077/data/www/atri-energo.ru/usr/other/daemon/price.daemon.php';
		// $process = new \Daemon\DaemonProcess($command);
		// $processId = $process->getPid();
		// dd($process);
		// dd($processId);
	break;
	
	case 'start_notif':
		$command = '/opt/php72/bin/php -f /var/www/izhclient0077/data/www/atri-energo.ru/usr/other/daemon/notif.daemon.php';
		$process = new \Daemon\DaemonProcess($command);
		$processId = $process->getPid();
		dd($process);
		dd($processId);
	break;
	
	case 'status':
		$processId = 21808;
		$process = new \Daemon\DaemonProcess();
$process->setPid($processId);
$status = $process->status(); // возвращает true или false
// dd($process);
var_dump($status);

	break;

	case "stop":
		$processId = 21808;
		$process = new \Daemon\DaemonProcess();
		$process->setPid($processId);
		$stopped = $process->stop(); // возвращает true или false*/
		var_dump($stopped);
	break;
}
