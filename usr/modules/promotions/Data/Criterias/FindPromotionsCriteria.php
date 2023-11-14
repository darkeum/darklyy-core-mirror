<?php

namespace Promotions\Data\Criterias;

use Boot\System\Core\Abstracts\Criterias\Criteria;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class FindPromotionsCriteria extends Criteria
{
    private int $id;

    public function __construct($role)
    {
        $this->id = $role;
    }

    public function apply($model, PrettusRepositoryInterface $repository)
    {
        return $model->where('id', $this->id);
    }
}
