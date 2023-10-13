<?php

namespace QuantaQuirk\Routing\Controllers;

interface HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return \QuantaQuirk\Routing\Controllers\Middleware|array
     */
    public static function middleware();
}
