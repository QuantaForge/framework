<?php

namespace QuantaQuirk\Routing\Exceptions;

use QuantaQuirk\Http\Response;
use RuntimeException;
use Throwable;

class StreamedResponseException extends RuntimeException
{
    /**
     * The actual exception thrown during the stream.
     *
     * @var \Throwable
     */
    public $originalException;

    /**
     * Create a new exception instance.
     *
     * @param  \Throwable  $originalException
     * @return void
     */
    public function __construct(Throwable $originalException)
    {
        $this->originalException = $originalException;

        parent::__construct($originalException->getMessage());
    }

    /**
     * Render the exception.
     *
     * @return \QuantaQuirk\Http\Response
     */
    public function render()
    {
        return new Response('');
    }

    /**
     * Get the actual exception thrown during the stream.
     *
     * @return \Throwable
     */
    public function getInnerException()
    {
        return $this->originalException;
    }
}
