<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}
global $core, $config;

$module = 'certificates';
$data = array();
$max = 1;
$queryMax = $db->query("SELECT MAX(views) as max FROM ".DB_PREFIX."_certificates WHERE active!='0'");
if($db->numRows($queryMax) > 0) 
{
	$rMax = $db->getRow($queryMax);
	$max = $rMax['max'];
}
$queryDB = $db->query("SELECT * FROM ".DB_PREFIX."_certificates WHERE active!='0' ORDER BY date DESC");
if($db->numRows($queryDB) > 0) 
{	
	while($r = $db->getRow($queryDB)) 
	{
		$priority = round(($r['views']/$max), 1);		
		$link = $config['url'].($r['category'] !== ',0,' ? '/certificates/' . $core->getCat('certificates', $r['category'], 'development') . '/' : '/certificates/').$r['altname'].'.html';
		$data[] = array(
			'module' => $module,
			'pid' => $r['id'],
			'loc' => $link,
			'priority' => (($priority > 0.5) ? $priority : '0.5'),
			'changefreq' => '',
			'lastmod' => $r['date_edit'],
			'title' => $r['title'],
			'description' => (!empty($r['description']) ? $r['description'] : ''),
			'keywords' => (!empty($r['keywords']) ? $r['keywords'] : ''),
		);	
	}	
}
$sm = new \Sitemap\Sitemap();
$sm->setDataSitamap($module, $data);


