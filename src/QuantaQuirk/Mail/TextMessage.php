<?php

namespace QuantaQuirk\Mail;

use QuantaQuirk\Support\Traits\ForwardsCalls;

/**
 * @mixin \QuantaQuirk\Mail\Message
 */
class TextMessage
{
    use ForwardsCalls;

    /**
     * The underlying message instance.
     *
     * @var \QuantaQuirk\Mail\Message
     */
    protected $message;

    /**
     * Create a new text message instance.
     *
     * @param  \QuantaQuirk\Mail\Message  $message
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Embed a file in the message and get the CID.
     *
     * @param  string|\QuantaQuirk\Contracts\Mail\Attachable|\QuantaQuirk\Mail\Attachment  $file
     * @return string
     */
    public function embed($file)
    {
        return '';
    }

    /**
     * Embed in-memory data in the message and get the CID.
     *
     * @param  string|resource  $data
     * @param  string  $name
     * @param  string|null  $contentType
     * @return string
     */
    public function embedData($data, $name, $contentType = null)
    {
        return '';
    }

    /**
     * Dynamically pass missing methods to the underlying message instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->message, $method, $parameters);
    }
}
