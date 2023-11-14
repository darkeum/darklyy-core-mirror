<?php
	define('ACCESS', true);
	$login = 'u83';
	$secret = 'TeuT9M3r0QEMWoH0iukCpQiWPDQyK9Hl';
	$url = 'https://api.izhnet.ru/amocrm/asterisk/83/?_login='.$login.'&_secret='.$secret.'&_action=cdr';
	$json = file_get_contents($url);
	$data = json_decode($json, true);
//	echo '<pre>';
//	print_r($data['data']);
	
	require_once '../../etc/db.config.php'; 
//	echo $dbhost;
	$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, '3311') 
    or die("Ошибка " . mysqli_error($link)); 
	$query ="SELECT uniqueid FROM ".$prefix."_calls";	
	$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));	
	while($rr = @mysqli_fetch_assoc($result))
	{
		$arr_res[] = $rr['uniqueid'];
	}
	foreach ($data['data'] as $item)
	{
		
	//	print_r(array_search($uniqueid, $arr_res));
		$arr = $item;
		foreach ($arr as $key => $value)
		{			
			$$key = $value;
		}
		//echo $calldate;2018-01-31 04:38:50
	//		print_r($duration);
		//		print_r($data['data']);
		if (!in_array($uniqueid, $arr_res, true))
		{
			$date = $calldate;
			$parseDate = explode(' ', $date);
			$subDate = explode('-', $parseDate[0]);
			if(isset($parseDate[1]))
			{
				$subTime = explode(':', $parseDate[1]);
			}
			else
			{
				$subTime[0] = 12;
				$subTime[1] = 0;
			}
			echo 'rtg';
			$date = gmmktime($subTime[0], $subTime[1], $subTime[2], $subDate[1], $subDate[2], $subDate[0]);			
			$query = "INSERT INTO `" . $prefix . "_calls` ( `id` , `src` , `dst` ,  `calldate` , `duration` , `billsec` , `uniqueid` , `recordingfile` , `status`) VALUES (NULL, '" . $src . "', '" . $dst . "', '" . $date . "', '" . $duration . "', '" . $billsec . "', '" . $uniqueid . "', '" . $recordingfile . "', '1');";
			$insert = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
		}
	} 	
	mysqli_close($link);		
?>
	