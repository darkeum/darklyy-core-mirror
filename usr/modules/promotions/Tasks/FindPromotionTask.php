<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Promotions\Tasks;

use Boot\System\Core\Abstracts\Tasks\Task;
use Promotions\Data\Criterias\FindPromotionsCriteria;
use Promotions\Data\Repositories\PromotionsRepository;
use Promotions\Data\Criterias\AvailablePromotionsCriteria;

class FindPromotionTask extends Task
{
    protected PromotionsRepository $repository;

    public function __construct(PromotionsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(string|int $id): mixed
    {
        $query['id'] = $id;
        return $this->repository->pushCriteria(new AvailablePromotionsCriteria())->findWhere($query)->first();
    }

 }
