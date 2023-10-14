<?php

namespace QuantaForge\Tests\Integration\Auth;

use QuantaForge\Auth\Access\Events\GateEvaluated;
use QuantaForge\Support\Facades\Event;
use QuantaForge\Support\Facades\Gate;
use QuantaForge\Tests\Integration\Auth\Fixtures\AuthenticationTestUser;
use QuantaForge\Tests\Integration\Auth\Fixtures\Policies\AuthenticationTestUserPolicy;
use Orchestra\Testbench\TestCase;

class GatePolicyResolutionTest extends TestCase
{
    public function testGateEvaluationEventIsFired()
    {
        Event::fake();

        Gate::check('foo');

        Event::assertDispatched(GateEvaluated::class);
    }

    public function testPolicyCanBeGuessedUsingClassConventions()
    {
        $this->assertInstanceOf(
            AuthenticationTestUserPolicy::class,
            Gate::getPolicyFor(AuthenticationTestUser::class)
        );

        $this->assertInstanceOf(
            AuthenticationTestUserPolicy::class,
            Gate::getPolicyFor(Fixtures\Models\AuthenticationTestUser::class)
        );

        $this->assertNull(
            Gate::getPolicyFor(static::class)
        );
    }

    public function testPolicyCanBeGuessedUsingCallback()
    {
        Gate::guessPolicyNamesUsing(function () {
            return AuthenticationTestUserPolicy::class;
        });

        $this->assertInstanceOf(
            AuthenticationTestUserPolicy::class,
            Gate::getPolicyFor(AuthenticationTestUser::class)
        );
    }

    public function testPolicyCanBeGuessedMultipleTimes()
    {
        Gate::guessPolicyNamesUsing(function () {
            return [
                'App\\Policies\\TestUserPolicy',
                AuthenticationTestUserPolicy::class,
            ];
        });

        $this->assertInstanceOf(
            AuthenticationTestUserPolicy::class,
            Gate::getPolicyFor(AuthenticationTestUser::class)
        );
    }
}
