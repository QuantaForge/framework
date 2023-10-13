<?php

namespace QuantaQuirk\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \QuantaQuirk\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
