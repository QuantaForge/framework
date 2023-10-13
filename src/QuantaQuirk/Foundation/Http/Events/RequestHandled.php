<?php

namespace QuantaQuirk\Foundation\Http\Events;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \QuantaQuirk\Http\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \QuantaQuirk\Http\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @param  \QuantaQuirk\Http\Response  $response
     * @return void
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
