<?php

namespace QuantaForge\Events;

use Closure;

if (! function_exists('QuantaForge\Events\queueable')) {
    /**
     * Create a new queued Closure event listener.
     *
     * @param  \Closure  $closure
     * @return \QuantaForge\Events\QueuedClosure
     */
    function queueable(Closure $closure)
    {
        return new QueuedClosure($closure);
    }
}
