<?php

namespace QuantaQuirk\Queue\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \QuantaQuirk\Contracts\Queue\Queue
     */
    public function connect(array $config);
}
