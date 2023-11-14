<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\UI\WEB\Controllers;

use Boot\System\Http\Request;
use Developers\Actions\GitAction;
use Boot\Abstracts\Controllers\WebController;

class GitController extends WebController
{
	static function index(Request $request)
	{
		$git = app(GitAction::class)->run($request);
		return view('developers::manager.git', [
			"command" => $git['command'],
			"output" => $git['output'],
		]);
	}
}
