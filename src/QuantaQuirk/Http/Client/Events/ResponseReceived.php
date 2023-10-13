<?php

namespace QuantaQuirk\Http\Client\Events;

use QuantaQuirk\Http\Client\Request;
use QuantaQuirk\Http\Client\Response;

class ResponseReceived
{
    /**
     * The request instance.
     *
     * @var \QuantaQuirk\Http\Client\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \QuantaQuirk\Http\Client\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Http\Client\Request  $request
     * @param  \QuantaQuirk\Http\Client\Response  $response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
