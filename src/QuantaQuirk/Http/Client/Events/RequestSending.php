<?php

namespace QuantaQuirk\Http\Client\Events;

use QuantaQuirk\Http\Client\Request;

class RequestSending
{
    /**
     * The request instance.
     *
     * @var \QuantaQuirk\Http\Client\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Http\Client\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
