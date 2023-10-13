<?php

namespace QuantaQuirk\Tests\Routing;

use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Routing\RouteSignatureParameters;
use QuantaQuirk\SerializableClosure\SerializableClosure;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

class RouteSignatureParametersTest extends TestCase
{
    public function test_it_can_extract_the_route_action_signature_parameters()
    {
        $callable = function (SignatureParametersUser $user) {
            return $user;
        };

        $action = ['uses' => serialize(
            new SerializableClosure($callable)
        )];

        $parameters = RouteSignatureParameters::fromAction($action);

        $this->assertContainsOnlyInstancesOf(ReflectionParameter::class, $parameters);
        $this->assertSame('user', $parameters[0]->getName());
    }
}

class SignatureParametersUser extends Model
{
    //
}