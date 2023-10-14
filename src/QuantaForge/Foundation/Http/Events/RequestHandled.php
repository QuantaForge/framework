<?php

namespace QuantaForge\Foundation\Http\Events;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \QuantaForge\Http\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \QuantaForge\Http\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @param  \QuantaForge\Http\Response  $response
     * @return void
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
