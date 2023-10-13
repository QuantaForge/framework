<?php

namespace QuantaQuirk\Routing\Matching;

use QuantaQuirk\Http\Request;
use QuantaQuirk\Routing\Route;

class UriValidator implements ValidatorInterface
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
        $path = rtrim($request->getPathInfo(), '/') ?: '/';

        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }
}
