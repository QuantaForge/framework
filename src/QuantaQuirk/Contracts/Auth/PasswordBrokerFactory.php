<?php

namespace QuantaQuirk\Contracts\Auth;

interface PasswordBrokerFactory
{
    /**
     * Get a password broker instance by name.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Contracts\Auth\PasswordBroker
     */
    public function broker($name = null);
}
