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

class GetPromotionsTask extends Task
{
    protected PromotionsRepository $repository;

    public function __construct(PromotionsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(bool $skipPagination = false): mixed
    {
        $repository = $this->addRequestCriteria()->available()->repository;

        return $skipPagination ? $repository->all() : $repository->paginate();
    }

    public function available(): self
    {
        $this->repository->pushCriteria(new AvailablePromotionsCriteria());
        return $this;
    }

    public function find($promotion): self
    {
        $this->repository->pushCriteria(new FindPromotionsCriteria($promotion));
        return $this;
    }
}
