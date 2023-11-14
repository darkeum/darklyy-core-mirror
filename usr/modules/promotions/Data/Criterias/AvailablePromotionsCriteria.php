<?php

namespace Promotions\Data\Criterias;


use Boot\Support\Carbon;
use Boot\System\Core\Abstracts\Criterias\Criteria;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class AvailablePromotionsCriteria extends Criteria
{
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        $now = Carbon::now()->toDateString();
        return $model->where('status', 1)->where('date_start', '<=', $now)->where('date_end', '>=', $now);
    }
}
