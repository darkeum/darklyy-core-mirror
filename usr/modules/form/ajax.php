<?php
global $db, $allowFuncs, $url, $core;
if (isset($allowFuncs))
{
$allowFuncs = array_merge($allowFuncs, ['formHeatExchanger' ,'formHeatExchangerAuto', 'fileAxax']);
}

function formHeatExchanger()
{
	global $core, $db;	
	$hc = new \Calculation\HeatCalculation();
	$result = $hc->getHeatOrder($_POST);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	exit;	
}