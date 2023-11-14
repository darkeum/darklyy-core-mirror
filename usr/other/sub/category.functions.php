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
function get_category_main($numb = 100) {
	global $db;
		$result = '';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE module = 'catalog_v2' AND parent_id = '0' AND `hidden` = '0' ORDER BY position ASC LIMIT ".$numb."");	
			if($db->numRows($query) > 0)
			{
				$i = 0;
				while($object = $db->getRow($query))
				{
					$i++;
					if ($i == 1 || $i == 3 || $i == 5 || $i == 7)
					{
						$result .='<div class="item ">';
					}
					$result .= '<div class="item-wrap">
	<div class="item-detail">				
		<div class="item-img products-thumb" style="   height: 250px;    vertical-align: middle;    padding-top: 50px;">	
			<a href="/catalog_v2/'.$object['altname'].'"><div class=""><img width="300" height="300" src="'.$object['preview'].'" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="" srcset="'.$object['preview'].' 300w,  '.$object['preview'].' 600w" sizes="(max-width: 300px) 100vw, 300px"></div></a>
		</div>									
		<div class="item-content">	
			
			<div class="item-price">
					<span><span class="woocommerce-Price-amount amount"><a style="color: #fa381d;    font-size: 17px;" href="/catalog_v2/'.$object['altname'].'" title="'.$object['name'].'">'.$object['name'].'</a></span></span>
				</div>
			<div class="item-bottom">
				<a rel="nofollow" href="/catalog_v2/'.$object['altname'].'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Просмотреть</a>
			</div>	
		</div>								
	</div>
</div>			';	

if ($i == 2 || $i == 4 ||$i == 6 || $i == 8)
					{
						$result .='</div>';
					}		
								}
							
								
			}
			return $result;
	
}

function get_category_main_new($numb = 100) {
	global $db;
		$result = '';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_categories WHERE module = 'catalog_v2' AND parent_id = '0' AND `hidden` = '0' ORDER BY position ASC LIMIT ".$numb."");	
			if($db->numRows($query) > 0)
			{
				$i = 0;
				while($object = $db->getRow($query))
				{
			$preview_array = json_decode($object['preview'], true);
					$i++;
					if ($i == 1 || $i == 3 || $i == 5 || $i == 7)
					{
						$result .='<div class="item ">';
					}
					$result .= '<div class="item-wrap">
	<div class="item-detail">				
		<div class="item-img products-thumb" >	
			<a href="/catalog_v2/'.$object['altname'].'"><div class="">
			<picture >
					<source srcset="'.($preview_array['full_webp'] ?? null).'" type="image/webp">
					<img src="'.($preview_array['full'] ?? null).'" class="attachment-shop_catalog size-shop_catalog">
				</picture>
		
			
			</div></a>
		</div>									
		<div class="item-content">	
			<div class="item-price">
					<span><span class="catalog-card-title"><a href="/catalog_v2/'.$object['altname'].'">'.$object['name'].'</a></span></span>
				</div>
			<div class="item-bottom">
				<a href="/catalog_v2/'.$object['altname'].'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Просмотреть</a>
			</div>	
		</div>								
	</div>
</div>			';	

if ($i == 2 || $i == 4 ||$i == 6 || $i == 8)
					{
						$result .='</div>';
					}		
								}
							
								
			}
			return $result;
	
}

