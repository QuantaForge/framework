<?php

namespace QuantaForge\Contracts\Auth;

interface PasswordBrokerFactory
{
    /**
     * Get a password broker instance by name.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Auth\PasswordBroker
     */
    public function broker($name = null);
}
