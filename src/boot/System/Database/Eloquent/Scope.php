<?php

namespace Boot\System\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Boot\System\Database\Eloquent\Builder  $builder
     * @param  \Boot\System\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
