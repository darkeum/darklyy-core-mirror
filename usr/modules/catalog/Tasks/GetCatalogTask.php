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
use Catalog\Data\Criterias\FindCatalogCriteria;
use Catalog\Data\Repositories\CatalogRepository;
use Catalog\UI\API\Requests\GetAllCatalogRequest;

class GetCatalogTask extends Task
{
    protected CatalogRepository $repository;

    public function __construct(CatalogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(GetAllCatalogRequest $request, bool $skipPagination = false): mixed
    {
        $repository = $this->addRequestCriteria()->repository->with(['catalog_manufacture']);

        return $skipPagination ? $repository->all() : $repository->paginate();
    } 

    public function find($promotion): self
    {
        $this->repository->pushCriteria(new FindCatalogCriteria($promotion));
        return $this;
    }
}
