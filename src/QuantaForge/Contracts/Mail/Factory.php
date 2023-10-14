<?php

namespace QuantaForge\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
