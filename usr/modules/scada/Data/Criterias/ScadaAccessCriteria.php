<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Data\Criterias;

use Boot\Support\Facades\Auth;
use Boot\System\Core\Abstracts\Criterias\Criteria;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class ScadaAccessCriteria extends Criteria
{
    public function apply($model, PrettusRepositoryInterface $repository)
    {    
        return $model->whereHas("client", function (\Boot\System\Database\Model\Builder $query) {
			$query->where('uid',  Auth::id());
		});
    }
}
