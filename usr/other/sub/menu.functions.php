<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/
function get_tree_main($tree, $parent_id, $par = 0, $numb = 100) {
	$html = '';
	$i = 0;
	global $core, $url;
	
	foreach ($tree as $cid => $row) 
	{
		if ($row['hidden'] == 0)
		{
			if(isset($row['parent_id'])){
				$parent = $row['parent_id']; 
			} else {
				$parent = $row['parent']; 
			}
			
			if(isset($row['name'])){
				$title = $row['name']; 
			} else {
				$title = $row['title']; 
			}
			if ($parent == 0)
			{
				$i++;
			}
			if ($parent == 0 && $numb >= $i)
			{
				if ($parent == $parent_id) 
				{
					$html .= ' <li class="vertical-item level1 vertical_drop css_parent '.(isset($url[1]) && $url[1] == $row['altname'] ? 'active' : '').' ">' . "\n";
					$html .= '<a class="menu-link" href="catalog/' . $row['altname'] . '">';
					if ($parent == 0)
					{
						$html .= '  <span class="icon_items"><img class="img-responsive" alt="smartphone" src="usr/tpl/atri/assest/images/menu_icon.png" /></span>
						<span class="menu-title">'.$title.'</span>   <span class="btn-dropdown-toggle"><i class="fa fa-caret-down"></i></span>';
					}
					else
					{
						$html .=  $title;
					}				
					$html .=  "</a>";
					if ($par == 1)
					{
						$html .= '    ' . get_tree_main($tree, $cid, 2);
					}
					else
					{
						$html .= '    ' . get_tree_main($tree, $cid, 1);
					}
					$html .= '</li>' . "\n";
				}
			}
			elseif($parent == 0 && $numb < $i)
			{
					$html .= ' <li class="vertical-item level1">' . "\n";
					$html .= '<a class="menu-link" href="/catalog">
					  <span class="icon_items"><img class="img-responsive" alt="smartphone" src="usr/tpl/atri/assest/images/menu_icon.png" /></span>
						<span class="menu-title">Посмотреть все</span>
				</a>';
					$html .= '</li>' . "\n";
					break;
			}
			else
			{
				if ($parent == $parent_id) 
				{				
					$html .= ' <li class="vertical-item level2 '. (is_sub($tree, $cid) ? 'sub-dropdown' : '') .'">' . "\n";
					$html .= '<a class="menu-link" href="catalog/' . $row['altname'] . '">';
					$html .=  $title;				
					$html .=  "</a>";
					if (is_sub($tree, $cid)) { $html .= '<span class="touch-button dropdown-toggle"><i class="fa fa-caret-down"></i></span>';}
					if ($par == 1)
					{
						$html .= '    ' . get_tree_main($tree, $cid, 2);
					}
					else
					{
						$html .= '    ' . get_tree_main($tree, $cid, 1);
					}
					$html .= '</li>' . "\n";
				}
			}
		}
	 }
	 if ($par == 1)
	 {
		return $html ? '  <ul class="vertical-drop drop-css drop-lv1" style="width: 200px;"> 
		' . $html . '</ul>' : ''; 
	 }
	 elseif($par == 2)
	 {
		 return $html ? '     <span class="touch-button dropdown-toggle"><i class="fa fa-caret-down"></i></span>  <ul class="vertical-drop drop-lv2 dropdown-content">' . $html . '</ul>
				 ' : ''; 
	 }
	 else
	 {	 
		return $html ? '  <ul class="vertical-group">' . $html . '</ul>' : '';
	 }
}



function get_menu($tree, $parent_id, $par = 0, $numb = 100) {
	$html = '';
	$i = 0;
	global $core, $url;
	
	foreach ($tree as $cid => $row) 
	{
		if ($row['hidden'] == 0)
		{
			if(isset($row['parent_id'])){
				$parent = $row['parent_id']; 
			} else {
				$parent = $row['parent']; 
			}

			if(isset($row['name'])){
				$title = $row['name']; 
			} else {
				$title = $row['title']; 
			}


			if ($parent == 0)
			{
				$i++;
			}
			if ($parent == 0 && $numb >= $i)
			{
				if ($parent == $parent_id) 
				{
					$html .= ' <li class="vertical-item level1 vertical_drop css_parent '.(isset($url[1]) && $url[1] == $row['altname'] ? 'active' : '').' ">' . "\n";
					$html .= '<a class="menu-link" href="catalog_v2/' . $row['altname'] . '">';
					if ($parent == 0)
					{
						$html .= ' 
						<span class="menu-title">'.$title.'</span>   <span class="btn-dropdown-toggle"><i class="fa fa-caret-down"></i></span>';
					}
					else
					{
						$html .=  $title;
					}				
					$html .=  "</a>";
					if ($par == 1)
					{
						$html .= '    ' . get_menu($tree, $cid, 2);
					}
					else
					{
						$html .= '    ' . get_menu($tree, $cid, 1);
					}
					$html .= '</li>' . "\n";
				}
			}
			elseif($parent == 0 && $numb < $i)
			{
					$html .= ' <li class="vertical-item level1">' . "\n";
					$html .= '<a class="menu-link" href="/catalog_v2">
					
						<span class="menu-title">Посмотреть все</span>
				</a>';
					$html .= '</li>' . "\n";
					break;
			}
			else
			{
				if ($parent == $parent_id) 
				{				
					$html .= ' <li class="vertical-item level2 '. (is_sub($tree, $cid) ? 'sub-dropdown' : '') .'">' . "\n";
					$html .= '<a class="menu-link" href="catalog_v2/' . $row['altname'] . '">';
					$html .=  $title;				
					$html .=  "</a>";
					if (is_sub($tree, $cid)) { $html .= '<span class="touch-button dropdown-toggle"><i class="fa fa-caret-down"></i></span>';}
					if ($par == 1)
					{
						$html .= '    ' . get_menu($tree, $cid, 2);
					}
					else
					{
						$html .= '    ' . get_menu($tree, $cid, 1);
					}
					$html .= '</li>' . "\n";
				}
			}
		}
	 }
	 if ($par == 1)
	 {
		return $html ? '  <ul class="vertical-drop drop-css drop-lv1" style="width: 200px;"> 
		' . $html . '</ul>' : ''; 
	 }
	 elseif($par == 2)
	 {
		 return $html ? '     <span class="touch-button dropdown-toggle"><i class="fa fa-caret-down"></i></span>  <ul class="vertical-drop drop-lv2 dropdown-content">' . $html . '</ul>
				 ' : ''; 
	 }
	 else
	 {	 
		return $html ? '  <ul class="vertical-group">' . $html . '</ul>' : '';
	 }
}

function get_menu_main($tree, $parent_id, $par = 0, $numb = 100, $home = false) {
	$html = '';
	$i = 0;
	global $core, $url;
	if(is_array($tree)){

		foreach ($tree as $cid => $row) 
		{
			if ($row['hidden'] == 0)
			{
				if(isset($row['parent_id'])){
					$parent = $row['parent_id']; 
				} else {
					$parent = $row['parent']; 
				}
	
				if(isset($row['name'])){
					$title = $row['name']; 
				} else {
					$title = $row['title']; 
				}
	
				if ($parent == 0)
				{
					$i++;
				}
				if ($parent == 0 && $numb >= $i)
				{
					if ($parent == $parent_id) 
					{
						$html .= ' <li class="dropdown menu-smartphone flytheme-menu-custom level1">' . "\n";
						$html .= '<a class="item-link dropdown-toggle" href="catalog_v2/' . $row['altname'] . '">';
						if ($parent == 0)
						{
							$html .= '<span class="have-title">
							<span class="menu-title">'.$title.'</span></span>';
						}
						else
						{
							$html .=  $title;
						}				
						$html .=  "</a>";
						if ($par == 1)
						{
							$html .= '    ' . get_menu_main($tree, $cid, 2, 100, $home);
						}
						else
						{
							$html .= '    ' . get_menu_main($tree, $cid, 1, 100, $home);
						}
						$html .= '</li>' . "\n";
					}
				}
				elseif($parent == 0 && $numb < $i)
				{
						$html .= ' <li class="dropdown menu-smartphone flytheme-menu-custom level1">' . "\n";
						$html .= '<a class="menu-link" href="/catalog_v2">
						  <span class="icon_items"></span>
							<span class="menu-title">Посмотреть все</span>
					</a>';
						$html .= '</li>' . "\n";
						break;
				}
				else
				{
					if ($parent == $parent_id) 
					{				
						$html .= ' <li class=" column-1 '. (is_sub($tree, $cid) ? 'dropdown-submenu' : '') .'">' . "\n";
						$html .= '<a class="" href="catalog_v2/' . $row['altname'] . '">';
						$html .=  $title;				
						$html .=  "</a>";
						if (is_sub($tree, $cid)) { $html .= '';}
						if ($par == 1)
						{
							$html .= '    ' . get_menu_main($tree, $cid, 2, 100, $home);
						}
						else
						{
							$html .= '    ' . get_menu_main($tree, $cid, 1, 100, $home);
						}
						$html .= '</li>' . "\n";
					}
				}
			}
		 }
	}

	 if ($par == 1)
	 {
		return $html ? '  <ul class="dropdown-menu"> 
		' . $html . '</ul>' : ''; 
	 }
	 elseif($par == 2)
	 {
		 return $html ? '     <ul class="dropdown-sub nav-level2">' . $html . '</ul>
				 ' : ''; 
	 }
	 else
	 {	 
		return $html ? '  <ul id="menu-menu-vertical-category2-1" class=" vertical-megamenu '.($home ? 'home-menu-slider ' : '').'">' . $html . '</ul>' : '';
	 }
}

function is_sub($tree, $cidd)
{
	$flag = false;
	
	foreach ($tree as $cid => $row) 
	{
		if(isset($row['parent_id'])){
			$parent = $row['parent_id']; 
		} else {
			$parent = $row['parent']; 
		}
		if ($parent == $cidd)
		{
			$flag = true;
		}
	}
	return $flag;
}