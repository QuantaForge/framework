<?php

namespace QuantaForge\Contracts\Database\Query;

use QuantaForge\Database\Grammar;

interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \QuantaForge\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
