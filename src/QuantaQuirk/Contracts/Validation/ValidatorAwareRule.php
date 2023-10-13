<?php

namespace QuantaQuirk\Contracts\Validation;

use QuantaQuirk\Validation\Validator;

interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \QuantaQuirk\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator);
}
