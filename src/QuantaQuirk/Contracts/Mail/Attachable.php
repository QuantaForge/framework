<?php

namespace QuantaQuirk\Contracts\Mail;

interface Attachable
{
    /**
     * Get an attachment instance for this entity.
     *
     * @return \QuantaQuirk\Mail\Attachment
     */
    public function toMailAttachment();
}
