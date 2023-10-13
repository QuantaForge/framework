<?php

namespace QuantaQuirk\Routing\Events;

class RouteMatched
{
    /**
     * The route instance.
     *
     * @var \QuantaQuirk\Routing\Route
     */
    public $route;

    /**
     * The request instance.
     *
     * @var \QuantaQuirk\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Routing\Route  $route
     * @param  \QuantaQuirk\Http\Request  $request
     * @return void
     */
    public function __construct($route, $request)
    {
        $this->route = $route;
        $this->request = $request;
    }
}
