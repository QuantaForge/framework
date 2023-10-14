<?php

namespace QuantaForge\Contracts\Validation;

use QuantaForge\Validation\Validator;

interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \QuantaForge\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator);
}
