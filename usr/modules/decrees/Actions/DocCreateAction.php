<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\Actions;

use Decrees\Models\Decrees;
use Decrees\Tasks\DocCreateTask;
use Boot\Abstracts\Actions\Action;
use Decrees\Exceptions\DocTypesException;

class DocCreateAction extends Action
{
    public function run(int $id, string $type): Decrees
    {
		$available_type = ['decree', 'job', 'trip'];
		if(in_array($type, $available_type)){
			return app(DocCreateTask::class)->run(Decrees::findOrFail($id), $type);  
		} else {
			throw new DocTypesException();
		}            
    }
}
