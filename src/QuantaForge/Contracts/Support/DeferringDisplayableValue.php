<?php

namespace QuantaForge\Contracts\Support;

interface DeferringDisplayableValue
{
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return \QuantaForge\Contracts\Support\Htmlable|string
     */
    public function resolveDisplayableValue();
}
