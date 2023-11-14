<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $partner_conf;
$partner_conf = array();
$partner_conf['num'] = "25";
$partner_conf['comments_num'] = "10";
$partner_conf['fullLink'] = "1";
$partner_conf['noModer'] = "1";
$partner_conf['preModer'] = "10";
$partner_conf['related_partner'] = "5";
$partner_conf['addpartner'] = "1";
$partner_conf['showCat'] = "1";
$partner_conf['subLoad'] = "1";
$partner_conf['catCols'] = "3";
$partner_conf['showBreadcumb'] = "1";
$partner_conf['tags'] = "1";
$partner_conf['tags_num'] = "3";
$partner_conf['tagIll'] = "1";
$partner_conf['illFormat'] = "<b>{tag}</b>";
$partner_conf['limitStar'] = "5";
$partner_conf['starStyle'] = "";
$partner_conf['carma_rate'] = "1";
$partner_conf['carma_summ'] = "1";
$partner_conf['fileEditor'] = "1";
$partner_conf['imgFormats'] = "jpg,gif,png";
$partner_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$partner_conf['max_size'] = "15";
$partner_conf['thumb_width'] = "600";

