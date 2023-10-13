<?php

namespace QuantaQuirk\Support\Facades;

use Closure;
use QuantaQuirk\Process\Factory;

/**
 * @method static \QuantaQuirk\Process\PendingProcess command(array|string $command)
 * @method static \QuantaQuirk\Process\PendingProcess path(string $path)
 * @method static \QuantaQuirk\Process\PendingProcess timeout(int $timeout)
 * @method static \QuantaQuirk\Process\PendingProcess idleTimeout(int $timeout)
 * @method static \QuantaQuirk\Process\PendingProcess forever()
 * @method static \QuantaQuirk\Process\PendingProcess env(array $environment)
 * @method static \QuantaQuirk\Process\PendingProcess input(\Traversable|resource|string|int|float|bool|null $input)
 * @method static \QuantaQuirk\Process\PendingProcess quietly()
 * @method static \QuantaQuirk\Process\PendingProcess tty(bool $tty = true)
 * @method static \QuantaQuirk\Process\PendingProcess options(array $options)
 * @method static \QuantaQuirk\Contracts\Process\ProcessResult run(array|string|null $command = null, callable|null $output = null)
 * @method static \QuantaQuirk\Process\InvokedProcess start(array|string|null $command = null, callable $output = null)
 * @method static \QuantaQuirk\Process\PendingProcess withFakeHandlers(array $fakeHandlers)
 * @method static \QuantaQuirk\Process\PendingProcess|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \QuantaQuirk\Process\PendingProcess|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \QuantaQuirk\Process\FakeProcessResult result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
 * @method static \QuantaQuirk\Process\FakeProcessDescription describe()
 * @method static \QuantaQuirk\Process\FakeProcessSequence sequence(array $processes = [])
 * @method static bool isRecording()
 * @method static \QuantaQuirk\Process\Factory recordIfRecording(\QuantaQuirk\Process\PendingProcess $process, \QuantaQuirk\Contracts\Process\ProcessResult $result)
 * @method static \QuantaQuirk\Process\Factory record(\QuantaQuirk\Process\PendingProcess $process, \QuantaQuirk\Contracts\Process\ProcessResult $result)
 * @method static \QuantaQuirk\Process\Factory preventStrayProcesses(bool $prevent = true)
 * @method static bool preventingStrayProcesses()
 * @method static \QuantaQuirk\Process\Factory assertRan(\Closure|string $callback)
 * @method static \QuantaQuirk\Process\Factory assertRanTimes(\Closure|string $callback, int $times = 1)
 * @method static \QuantaQuirk\Process\Factory assertNotRan(\Closure|string $callback)
 * @method static \QuantaQuirk\Process\Factory assertDidntRun(\Closure|string $callback)
 * @method static \QuantaQuirk\Process\Factory assertNothingRan()
 * @method static \QuantaQuirk\Process\Pool pool(callable $callback)
 * @method static \QuantaQuirk\Contracts\Process\ProcessResult pipe(callable|array $callback, callable|null $output = null)
 * @method static \QuantaQuirk\Process\ProcessPoolResults concurrently(callable $callback, callable|null $output = null)
 * @method static \QuantaQuirk\Process\PendingProcess newPendingProcess()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \QuantaQuirk\Process\PendingProcess
 * @see \QuantaQuirk\Process\Factory
 */
class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return \QuantaQuirk\Process\Factory
     */
    public static function fake(Closure|array $callback = null)
    {
        return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
