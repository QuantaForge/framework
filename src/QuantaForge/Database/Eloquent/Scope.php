<?php

namespace QuantaForge\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \QuantaForge\Database\Eloquent\Builder  $builder
     * @param  \QuantaForge\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
