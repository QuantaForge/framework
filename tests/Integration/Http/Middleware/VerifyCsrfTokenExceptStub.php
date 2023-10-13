<?php

namespace QuantaQuirk\Tests\Integration\Http\Middleware;

use QuantaQuirk\Foundation\Http\Middleware\VerifyCsrfToken;

class VerifyCsrfTokenExceptStub extends VerifyCsrfToken
{
    public function checkInExceptArray($request)
    {
        return $this->inExceptArray($request);
    }

    public function setExcept(array $except)
    {
        $this->except = $except;

        return $this;
    }
}
