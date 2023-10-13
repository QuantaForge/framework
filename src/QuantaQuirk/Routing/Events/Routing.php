<?php

namespace QuantaQuirk\Routing\Events;

class Routing
{
    /**
     * The request instance.
     *
     * @var \QuantaQuirk\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }
}
