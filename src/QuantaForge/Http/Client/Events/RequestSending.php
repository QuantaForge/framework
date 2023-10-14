<?php

namespace QuantaForge\Http\Client\Events;

use QuantaForge\Http\Client\Request;

class RequestSending
{
    /**
     * The request instance.
     *
     * @var \QuantaForge\Http\Client\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaForge\Http\Client\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
