<?php

namespace QuantaQuirk\Contracts\Database\Query;

use QuantaQuirk\Database\Grammar;

interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \QuantaQuirk\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
