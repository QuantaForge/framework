<?php

namespace QuantaQuirk\Tests\Routing;

use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Routing\RouteAction;
use QuantaQuirk\SerializableClosure\SerializableClosure;
use PHPUnit\Framework\TestCase;

class RouteActionTest extends TestCase
{
    public function test_it_can_detect_a_serialized_closure()
    {
        $callable = function (RouteActionUser $user) {
            return $user;
        };

        $action = ['uses' => serialize(
            new SerializableClosure($callable)
        )];

        $this->assertTrue(RouteAction::containsSerializedClosure($action));

        $action = ['uses' => 'FooController@index'];

        $this->assertFalse(RouteAction::containsSerializedClosure($action));
    }
}

class RouteActionUser extends Model
{
    //
}
