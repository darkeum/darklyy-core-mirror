<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $catalog_conf;
$catalog_conf = array();
$catalog_conf['num'] = "100";
$catalog_conf['comments_num'] = "10";
$catalog_conf['fullLink'] = "1";
$catalog_conf['noModer'] = "1";
$catalog_conf['preModer'] = "10";
$catalog_conf['{varName}'] = "{var}";
$catalog_conf['addcatalog'] = "0";
$catalog_conf['showCat'] = "1";
$catalog_conf['subLoad'] = "1";
$catalog_conf['catCols'] = "3";
$catalog_conf['showBreadcumb'] = "1";
$catalog_conf['tags'] = "1";
$catalog_conf['tags_num'] = "3";
$catalog_conf['tagIll'] = "1";
$catalog_conf['illFormat'] = "<b>{tag}</b>";
$catalog_conf['limitStar'] = "5";
$catalog_conf['starStyle'] = "";
$catalog_conf['carma_rate'] = "1";
$catalog_conf['carma_summ'] = "1";
$catalog_conf['fileEditor'] = "1";
$catalog_conf['imgFormats'] = "jpg,gif,png";
$catalog_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$catalog_conf['max_size'] = "15";
$catalog_conf['thumb_width'] = "600";

