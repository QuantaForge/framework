<?php

namespace QuantaQuirk\Foundation\Http;

use QuantaQuirk\Auth\Access\AuthorizationException;
use QuantaQuirk\Auth\Access\Response;
use QuantaQuirk\Contracts\Container\Container;
use QuantaQuirk\Contracts\Validation\Factory as ValidationFactory;
use QuantaQuirk\Contracts\Validation\ValidatesWhenResolved;
use QuantaQuirk\Contracts\Validation\Validator;
use QuantaQuirk\Http\Request;
use QuantaQuirk\Routing\Redirector;
use QuantaQuirk\Validation\ValidatesWhenResolvedTrait;
use QuantaQuirk\Validation\ValidationException;

class FormRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;

    /**
     * The container instance.
     *
     * @var \QuantaQuirk\Contracts\Container\Container
     */
    protected $container;

    /**
     * The redirector instance.
     *
     * @var \QuantaQuirk\Routing\Redirector
     */
    protected $redirector;

    /**
     * The URI to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirect;

    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute;

    /**
     * The controller action to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectAction;

    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'default';

    /**
     * Indicates whether validation should stop after the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = false;

    /**
     * The validator instance.
     *
     * @var \QuantaQuirk\Contracts\Validation\Validator
     */
    protected $validator;

    /**
     * Get the validator instance for the request.
     *
     * @return \QuantaQuirk\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        if ($this->validator) {
            return $this->validator;
        }

        $factory = $this->container->make(ValidationFactory::class);

        if (method_exists($this, 'validator')) {
            $validator = $this->container->call([$this, 'validator'], compact('factory'));
        } else {
            $validator = $this->createDefaultValidator($factory);
        }

        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        if (method_exists($this, 'after')) {
            $validator->after($this->container->call(
                $this->after(...),
                ['validator' => $validator]
            ));
        }

        $this->setValidator($validator);

        return $this->validator;
    }

    /**
     * Create the default validator instance.
     *
     * @param  \QuantaQuirk\Contracts\Validation\Factory  $factory
     * @return \QuantaQuirk\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory)
    {
        $rules = method_exists($this, 'rules') ? $this->container->call([$this, 'rules']) : [];

        $validator = $factory->make(
            $this->validationData(), $rules,
            $this->messages(), $this->attributes()
        )->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($this->isPrecognitive()) {
            $validator->setRules(
                $this->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
            );
        }

        return $validator;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->all();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \QuantaQuirk\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \QuantaQuirk\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
                    ->errorBag($this->errorBag)
                    ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        if ($this->redirect) {
            return $url->to($this->redirect);
        } elseif ($this->redirectRoute) {
            return $url->route($this->redirectRoute);
        } elseif ($this->redirectAction) {
            return $url->action($this->redirectAction);
        }

        return $url->previous();
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     *
     * @throws \QuantaQuirk\Auth\Access\AuthorizationException
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            $result = $this->container->call([$this, 'authorize']);

            return $result instanceof Response ? $result->authorize() : $result;
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \QuantaQuirk\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException;
    }

    /**
     * Get a validated input container for the validated input.
     *
     * @param  array|null  $keys
     * @return \QuantaQuirk\Support\ValidatedInput|array
     */
    public function safe(array $keys = null)
    {
        return is_array($keys)
                    ? $this->validator->safe()->only($keys)
                    : $this->validator->safe();
    }

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        return data_get($this->validator->validated(), $key, $default);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Set the Validator instance.
     *
     * @param  \QuantaQuirk\Contracts\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the Redirector instance.
     *
     * @param  \QuantaQuirk\Routing\Redirector  $redirector
     * @return $this
     */
    public function setRedirector(Redirector $redirector)
    {
        $this->redirector = $redirector;

        return $this;
    }

    /**
     * Set the container implementation.
     *
     * @param  \QuantaQuirk\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
