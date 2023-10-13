<?php

namespace QuantaQuirk\Routing\Contracts;

use QuantaQuirk\Routing\Route;

interface CallableDispatcher
{
    /**
     * Dispatch a request to a given callable.
     *
     * @param  \QuantaQuirk\Routing\Route  $route
     * @param  callable  $callable
     * @return mixed
     */
    public function dispatch(Route $route, $callable);
}
