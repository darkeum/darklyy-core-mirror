<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $cert_conf;
$cert_conf = array();
$cert_conf['num'] = "10";
$cert_conf['comments_num'] = "10";
$cert_conf['fullLink'] = "1";
$cert_conf['noModer'] = "1";
$cert_conf['preModer'] = "10";
$cert_conf['related_news'] = "5";
$cert_conf['addNews'] = "1";
$cert_conf['showCat'] = "1";
$cert_conf['subLoad'] = "1";
$cert_conf['catCols'] = "3";
$cert_conf['showBreadcumb'] = "1";
$cert_conf['tags'] = "1";
$cert_conf['tags_num'] = "3";
$cert_conf['tagIll'] = "1";
$cert_conf['illFormat'] = "<b>{tag}</b>";
$cert_conf['limitStar'] = "5";
$cert_conf['starStyle'] = "";
$cert_conf['carma_rate'] = "1";
$cert_conf['carma_summ'] = "1";
$cert_conf['fileEditor'] = "1";
$cert_conf['imgFormats'] = "jpg,gif,png";
$cert_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$cert_conf['max_size'] = "15";
$cert_conf['thumb_width'] = "600";

