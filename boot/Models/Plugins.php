<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

class Plugins extends Model
{
    protected $fillable = ["title", "content", "file", "priority", "type", "service", "showin", "unshow", "groups", "free", "template", "categories", "xfields", "menu", "sitemap", "active"];
}
