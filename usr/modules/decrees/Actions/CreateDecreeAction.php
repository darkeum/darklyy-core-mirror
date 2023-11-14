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
use Boot\Support\Facades\Auth;
use Decrees\Tasks\NumbCreateTask;
use Boot\Abstracts\Actions\Action;
use Decrees\UI\WEB\Requests\CreateDecreeRequest;

class CreateDecreeAction extends Action
{
    public function run(CreateDecreeRequest $request): Decrees
    {
        $sanitizedData = $request->sanitizeInput([
			'type',
			'org',
			'comment',
			'started_at',
			'ended_at',
			'uid' => Auth::id(),
			'status' => 1,
		]);
        $decree = Decrees::create($sanitizedData);
        return app(NumbCreateTask::class)->run($decree);      
    }
}
