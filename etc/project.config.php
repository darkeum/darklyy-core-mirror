<?php
if (!defined('ACCESS')) 
{
    header('Location: /');
    exit;
}


global $project_conf;
$project_conf = array();
$project_conf['num'] = "55";
$project_conf['comments_num'] = "10";
$project_conf['fullLink'] = "1";
$project_conf['noModer'] = "1";
$project_conf['preModer'] = "10";
$project_conf['related_project'] = "5";
$project_conf['addproject'] = "1";
$project_conf['showCat'] = "1";
$project_conf['subLoad'] = "1";
$project_conf['catCols'] = "3";
$project_conf['showBreadcumb'] = "1";
$project_conf['tags'] = "1";
$project_conf['tags_num'] = "3";
$project_conf['tagIll'] = "1";
$project_conf['illFormat'] = "<b>{tag}</b>";
$project_conf['limitStar'] = "5";
$project_conf['starStyle'] = "";
$project_conf['carma_rate'] = "1";
$project_conf['carma_summ'] = "1";
$project_conf['fileEditor'] = "1";
$project_conf['imgFormats'] = "jpg,gif,png";
$project_conf['attachFormats'] = "zip,rar,mp3,avi,mp4,flv,3gp,torrent";
$project_conf['max_size'] = "15";
$project_conf['thumb_width'] = "600";

