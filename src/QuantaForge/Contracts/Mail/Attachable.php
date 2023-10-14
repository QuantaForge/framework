<?php

namespace QuantaForge\Contracts\Mail;

interface Attachable
{
    /**
     * Get an attachment instance for this entity.
     *
     * @return \QuantaForge\Mail\Attachment
     */
    public function toMailAttachment();
}
