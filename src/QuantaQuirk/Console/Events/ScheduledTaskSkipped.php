<?php

namespace QuantaQuirk\Console\Events;

use QuantaQuirk\Console\Scheduling\Event;

class ScheduledTaskSkipped
{
    /**
     * The scheduled event being run.
     *
     * @var \QuantaQuirk\Console\Scheduling\Event
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Console\Scheduling\Event  $task
     * @return void
     */
    public function __construct(Event $task)
    {
        $this->task = $task;
    }
}
