<?php

namespace QuantaQuirk\Support\Facades;

/**
 * @method static void resolveOptionsUsing(\Closure|null $resolver)
 * @method static void resolveTokenUsing(\Closure|null $resolver)
 * @method static void setUpProcess(callable $callback)
 * @method static void setUpTestCase(callable $callback)
 * @method static void setUpTestDatabase(callable $callback)
 * @method static void tearDownProcess(callable $callback)
 * @method static void tearDownTestCase(callable $callback)
 * @method static void callSetUpProcessCallbacks()
 * @method static void callSetUpTestCaseCallbacks(\QuantaQuirk\Foundation\Testing\TestCase $testCase)
 * @method static void callSetUpTestDatabaseCallbacks(string $database)
 * @method static void callTearDownProcessCallbacks()
 * @method static void callTearDownTestCaseCallbacks(\QuantaQuirk\Foundation\Testing\TestCase $testCase)
 * @method static mixed option(string $option)
 * @method static string|false token()
 *
 * @see \QuantaQuirk\Testing\ParallelTesting
 */
class ParallelTesting extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \QuantaQuirk\Testing\ParallelTesting::class;
    }
}
