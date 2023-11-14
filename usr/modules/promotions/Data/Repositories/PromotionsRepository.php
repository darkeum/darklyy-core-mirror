<?php

namespace Promotions\Data\Repositories;

use Boot\System\Core\Abstracts\Repositories\Repository;

class PromotionsRepository extends Repository
{
    protected $fieldSearchable = [
        'title' => 'like',
        'short' => 'like',
        'full' => 'like',
        'id' => '=',
        'created_at' => 'like',
    ];
    
    public function model(): string
    {
        return config('promotions.model');
    }
}
