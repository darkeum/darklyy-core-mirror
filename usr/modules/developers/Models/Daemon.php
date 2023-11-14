<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\Models;

use Boot\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
	
class Daemon extends Model{
	use SoftDeletes;

	protected $fillable = ["uid", "pid", "title", "command", "comment", "autostart", "status"];
}
