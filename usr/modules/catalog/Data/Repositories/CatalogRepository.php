<?php

namespace Catalog\Data\Repositories;

use Catalog\Models\Catalog;
use Boot\System\Core\Abstracts\Repositories\Repository;

class CatalogRepository extends Repository
{
    protected $fieldSearchable = [
        'category' => 'like',
        'short' => 'like',
        'full' => 'like',
        'id' => '=',
        'created_at' => 'like',
    ];
    
    public function model(): string
    {
        return Catalog::class;
    }
}
