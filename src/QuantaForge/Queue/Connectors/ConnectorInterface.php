<?php

namespace QuantaForge\Queue\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \QuantaForge\Contracts\Queue\Queue
     */
    public function connect(array $config);
}
