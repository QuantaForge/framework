<?php

namespace QuantaQuirk\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \QuantaQuirk\Broadcasting\Channel|\QuantaQuirk\Broadcasting\Channel[]|string[]|string
     */
    public function broadcastOn();
}
