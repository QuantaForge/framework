<?php

namespace QuantaQuirk\Contracts\Support;

interface DeferringDisplayableValue
{
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return \QuantaQuirk\Contracts\Support\Htmlable|string
     */
    public function resolveDisplayableValue();
}
