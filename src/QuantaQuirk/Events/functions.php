<?php

namespace QuantaQuirk\Events;

use Closure;

if (! function_exists('QuantaQuirk\Events\queueable')) {
    /**
     * Create a new queued Closure event listener.
     *
     * @param  \Closure  $closure
     * @return \QuantaQuirk\Events\QueuedClosure
     */
    function queueable(Closure $closure)
    {
        return new QueuedClosure($closure);
    }
}
