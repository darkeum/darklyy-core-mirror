<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $reviews_conf;
$reviews_conf = array();
$reviews_conf['num'] = "10";
$reviews_conf['comments_num'] = "10";
$reviews_conf['fullLink'] = "1";
$reviews_conf['noModer'] = "1";
$reviews_conf['preModer'] = "10";
$reviews_conf['related_news'] = "5";
$reviews_conf['addNews'] = "1";
$reviews_conf['showCat'] = "1";
$reviews_conf['subLoad'] = "1";
$reviews_conf['catCols'] = "3";
$reviews_conf['showBreadcumb'] = "1";
$reviews_conf['tags'] = "1";
$reviews_conf['tags_num'] = "3";
$reviews_conf['tagIll'] = "1";
$reviews_conf['illFormat'] = "<b>{tag}</b>";
$reviews_conf['limitStar'] = "5";
$reviews_conf['starStyle'] = "";
$reviews_conf['carma_rate'] = "1";
$reviews_conf['carma_summ'] = "1";
$reviews_conf['fileEditor'] = "1";
$reviews_conf['imgFormats'] = "jpg,gif,png";
$reviews_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$reviews_conf['max_size'] = "15";
$reviews_conf['thumb_width'] = "600";

