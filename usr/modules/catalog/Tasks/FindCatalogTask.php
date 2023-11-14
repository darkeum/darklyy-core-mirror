<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\Tasks;

use Boot\System\Core\Abstracts\Tasks\Task;
use Catalog\Data\Repositories\CatalogRepository;

class FindCatalogTask extends Task
{
    protected CatalogRepository $repository;

    public function __construct(CatalogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(string|int $id): mixed
    {
        $query['id'] = $id;
        return $this->repository->findWhere($query)->first();
    }

 }
