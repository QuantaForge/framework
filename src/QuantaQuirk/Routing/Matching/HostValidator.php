<?php

namespace QuantaQuirk\Routing\Matching;

use QuantaQuirk\Http\Request;
use QuantaQuirk\Routing\Route;

class HostValidator implements ValidatorInterface
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
        $hostRegex = $route->getCompiled()->getHostRegex();

        if (is_null($hostRegex)) {
            return true;
        }

        return preg_match($hostRegex, $request->getHost());
    }
}
