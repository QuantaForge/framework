<?php

namespace QuantaForge\Database\Concerns;

use QuantaForge\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \QuantaForge\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN '.$sql, $bindings);

        return new Collection($explanation);
    }
}
