<?php

namespace QuantaForge\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \QuantaForge\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
