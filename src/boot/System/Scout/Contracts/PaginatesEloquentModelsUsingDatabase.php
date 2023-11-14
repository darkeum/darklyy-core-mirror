<?php

namespace Boot\System\Scout\Contracts;

use Boot\System\Scout\Builder;

interface PaginatesEloquentModelsUsingDatabase
{
    /**
     * Paginate the given search on the engine.
     *
     * @param  \Boot\System\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  string  $pageName
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateUsingDatabase(Builder $builder, $perPage, $pageName, $page);

    /**
     * Paginate the given search on the engine using simple pagination.
     *
     * @param  \Boot\System\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  string  $pageName
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginateUsingDatabase(Builder $builder, $perPage, $pageName, $page);
}
