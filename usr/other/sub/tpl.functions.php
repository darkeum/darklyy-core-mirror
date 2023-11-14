<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/

use Boot\System\Twig\Enqueue;

function enqueueFile($matches=array())
{
	return Enqueue::enqueueFile($matches);	
}

