<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Tasks;

use Boot\System\Core\Abstracts\Tasks\Task;
use Scada\Data\Repositories\ScadaRepository;
use Scada\Data\Criterias\ScadaAccessCriteria;

class ScadaAllTask extends Task
{
    protected ScadaRepository $repository;

    public function __construct(ScadaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(bool $skipPagination = false): mixed
    {
        $repository = $this->addRequestCriteria()->access()->repository;

        return $skipPagination ? $repository->all() : $repository->paginate();
    }

    public function access(): self
    {
        $this->repository->pushCriteria(new ScadaAccessCriteria());
        return $this;
    }
}
