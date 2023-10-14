<?php

namespace QuantaForge\Http\Client\Events;

use QuantaForge\Http\Client\Request;
use QuantaForge\Http\Client\Response;

class ResponseReceived
{
    /**
     * The request instance.
     *
     * @var \QuantaForge\Http\Client\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \QuantaForge\Http\Client\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaForge\Http\Client\Request  $request
     * @param  \QuantaForge\Http\Client\Response  $response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
