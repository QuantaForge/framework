<?php

namespace QuantaForge\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \QuantaForge\Broadcasting\Channel|\QuantaForge\Broadcasting\Channel[]|string[]|string
     */
    public function broadcastOn();
}
