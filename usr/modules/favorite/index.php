<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

loadConfig('news');
function main($cat = null) 
{
	global $db, $config, $core, $tags, $news_conf, $url, $headTag, $cache;
	$uid = $core->auth->user_info['id'];
	if(empty($uid))
	{
		print_r('ry');
	}
	$core->tpl->loadFile('favorite/favorite.carcase');
	include('carcase_tag.php');
	$core->tpl->end();		
}


switch(isset($url[1]) ? $url[1] : null) 
{
	default:	
		if(eregStrt('.htm', mjsEnd($url)))
		{
			view(mjsEnd($url));
		}
		else
		{
			main(isset($url[1]) && $url[1] != 'page' ? $url[1] : false);
		}
	break;
	
	case 'addPost':
		errorLoad(); 
		news_add();
		break;
		
	case 'savePost':
		errorLoad(); 
		if($core->auth->isUser && $core->auth->group_info['addPost'] == 1)
		{
			$title = filter($_POST['title']);
			$translit = translit($title);
			$short = $_POST['short'];
			$status = $_POST['status'];
			$xfield = isset($_POST['xfield']) ? $_POST['xfield'] : '';
			$xfieldT = isset($_POST['xfieldT']) ? ($_POST['xfieldT']) : '';
			$qTr = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE altname = '" . $db->safesql($translit) . "'");
			if($db->numRows($qTr) > 0) $translit = $translit.gencode(3);
			$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';
			$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	
			if(is_array($category)) 
			{
				if(array_search(0, $category))
				{
					unset($category[array_serach(0, $category)]);
				}
				$firstCat = $category[0];
				unset($category[0]);
				$deleteCat = array_search($firstCat, $category);
				unset($category[$deleteCat]);
				$category[0] = $firstCat;
				ksort($category);
				$cats = '';
				foreach($category as $cid) 
				{
					$cats .= intval($cid) . ",";
				}
			}
			else 
			{
				$cats  = $category . ',';
			}
			
			$cats = ',' . $cats;
			$fieldsSer = '';
				if(!empty($xfield))
				{
					foreach($xfield as $xId => $xContent)
					{
						if(!empty($xContent) && $xId > 0 && !empty($xfieldT[$xId]))
						{
							$xContent = processText(filter($xContent, 'html'));
							$xId = intval($xId);
							$xfieldT[$xId] = processText(filter($xfieldT[$xId], 'title'));
							$fileds[$xId] = array($xfieldT[$xId], $xContent);
						}
					}
					
					$fieldsSer = serialize($fileds);
				}
			set_title(array(_NEWS, _ADD_NEWS));			
			if (!empty($edit_id) && ($core->auth->isModer||$core->auth->isAdmin))
			{
				
				
				$update = $db->query("UPDATE `" . DB_PREFIX . "_news` SET `author` = '" . $core->auth->user_info['nick'] . "', `cat` = '" . $cats . "', `altname` = '" . $translit . "', `fields` = '" . $fieldsSer . "', `active` = '" . $status . "' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
				
				
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $edit_id . "'");
				$news = $db->getRow($query);
				$short = fileInit('news', $news['id'], 'content', parseBB(processText(filter($_POST['short'], 'html')), $news['id']), 'user_temp'.$core->auth->user_id);
				$full = fileInit('news', $news['id'], 'content', parseBB(processText(filter($_POST['full'], 'html')), $news['id']), 'user_temp'.$core->auth->user_id);
				
				$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($title)) . "', `short` = '" . $db->safesql($short) . "', `full` = '" . $db->safesql($full) . "' WHERE `postId` ='" . $edit_id . "' AND `module` ='news' AND `lang`='" . $config['lang'] . "' LIMIT 1 ;");
				
				fileInit('news', $news['id'], 'dir', '', 'user_temp'.$core->auth->user_id);
				$core->tpl->info("Новость обновлена!");
				
				
			
			}
			elseif(!empty($title) && !empty($short) && empty($edit_id))
			{
				
				
				$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_news` ( `id` , `author` , `date` , `tags` , `cat` , `altname` , `allow_comments` , `allow_rating` , `allow_index` , `score` , `votes` , `views` , `comments` , `fields` , `groups` , `fixed` , `active` ) VALUES (NULL, '" . $core->auth->user_info['nick'] . "', '" . time() . "', '', '" . $cats . "', '" . $translit . "', '1', '1', '1', '0', '0', '0', '0', '" . $fieldsSer . "', ',0,', '0', '". (($core->auth->user_info['group'] == $news_conf['noModer']) ? '1' : '2')."');");
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE altname = '" . $db->safesql($translit) . "'");
				$news = $db->getRow($query);
				$short = fileInit('news', $news['id'], 'content', parseBB(processText(filter($_POST['short'], 'html')), $news['id']), 'user_temp'.$core->auth->user_id);
				$full = fileInit('news', $news['id'], 'content', parseBB(processText(filter($_POST['full'], 'html')), $news['id']), 'user_temp'.$core->auth->user_id);
				$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `short` , `full` , `lang` ) VALUES ('" . $news['id'] . "', 'news', '" . $db->safesql(processText($title)) . "', '" . $db->safesql($short) . "', '" . $db->safesql($full) . "' , '" . $config['lang'] . "');");
				fileInit('news', $news['id'], 'dir', '', 'user_temp'.$core->auth->user_id);
				$core->tpl->info("Ваша новость успешно добавлена. ".(($core->auth->user_info['group'] == $news_conf['noModer']) ? '' : 'Ожидайте модерации, если все поля заполнены верно ваша новость благополучно попадёт в новостную ленту нашего портала.'));
			}
			else
			{
				$core->tpl->info("Обязательные поля формы пусты! Вернитесь назад и попробуйте снова!");
			}
		}
		break;
	
	case "cat":
		main($url[2]);
	break;
	
	case "date":
		main('date');
	break;
	
	case "view":
		view();
		break;
		
	case 'edit':
	
		if($core->auth->isAdmin)
		{
			location(ADMIN.'/module/news/edit/'.$url[2]);
		}
		elseif($core->auth->isModer)
		{		
			$id = intval($url[2]);
			news_add($id);
		}
		break;
		
	case 'delete':
		if($core->auth->isAdmin)
		{
			location(ADMIN.'/module/news/delete/'.$url[2]);
		}
		break;
	
	case "tags":
		if(isset($url[2])) 
		{
			main('tag');
		}
		else
		{
			set_title(array(_NEWS, _TAGS));			
			$tag_query = $db->query("SELECT tag FROM " . DB_PREFIX . "_tags");
			while($tag = $db->getRow($tag_query)) 
			{
				$tags[] = $tag['tag'];
			}
			$tag_cloud = new TagsCloud;
			$tags_list = $tag_cloud->get_cloud($tags); 
			$core->tpl->title(_TAGS_ALL . ': ' . count($tags_list));
			$core->tpl->open();			
			echo '<div align="center">' . "\n";			
			foreach ($tags_list as $tag) 
			{
				echo $tag.' ';
			}			
			echo '</div>' . "\n";
			$core->tpl->close();
		}
		break;

		
}