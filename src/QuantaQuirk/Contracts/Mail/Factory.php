<?php

namespace QuantaQuirk\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
