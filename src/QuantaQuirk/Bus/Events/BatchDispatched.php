<?php

namespace QuantaQuirk\Bus\Events;

use QuantaQuirk\Bus\Batch;

class BatchDispatched
{
    /**
     * The batch instance.
     *
     * @var \QuantaQuirk\Bus\Batch
     */
    public $batch;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Bus\Batch  $batch
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }
}
