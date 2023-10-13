<?php

namespace QuantaQuirk\Routing\Matching;

use QuantaQuirk\Http\Request;
use QuantaQuirk\Routing\Route;

class MethodValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \QuantaQuirk\Routing\Route  $route
     * @param  \QuantaQuirk\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        return in_array($request->getMethod(), $route->methods());
    }
}
