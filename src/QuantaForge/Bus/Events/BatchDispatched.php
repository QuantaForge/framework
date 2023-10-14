<?php

namespace QuantaForge\Bus\Events;

use QuantaForge\Bus\Batch;

class BatchDispatched
{
    /**
     * The batch instance.
     *
     * @var \QuantaForge\Bus\Batch
     */
    public $batch;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaForge\Bus\Batch  $batch
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }
}
