<?php

namespace QuantaQuirk\Broadcasting;

use QuantaQuirk\Contracts\Broadcasting\HasBroadcastChannel;

class PrivateChannel extends Channel
{
    /**
     * Create a new channel instance.
     *
     * @param  \QuantaQuirk\Contracts\Broadcasting\HasBroadcastChannel|string  $name
     * @return void
     */
    public function __construct($name)
    {
        $name = $name instanceof HasBroadcastChannel ? $name->broadcastChannel() : $name;

        parent::__construct('private-'.$name);
    }
}
