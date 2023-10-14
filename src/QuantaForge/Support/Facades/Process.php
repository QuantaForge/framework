<?php

namespace QuantaForge\Support\Facades;

use Closure;
use QuantaForge\Process\Factory;

/**
 * @method static \QuantaForge\Process\PendingProcess command(array|string $command)
 * @method static \QuantaForge\Process\PendingProcess path(string $path)
 * @method static \QuantaForge\Process\PendingProcess timeout(int $timeout)
 * @method static \QuantaForge\Process\PendingProcess idleTimeout(int $timeout)
 * @method static \QuantaForge\Process\PendingProcess forever()
 * @method static \QuantaForge\Process\PendingProcess env(array $environment)
 * @method static \QuantaForge\Process\PendingProcess input(\Traversable|resource|string|int|float|bool|null $input)
 * @method static \QuantaForge\Process\PendingProcess quietly()
 * @method static \QuantaForge\Process\PendingProcess tty(bool $tty = true)
 * @method static \QuantaForge\Process\PendingProcess options(array $options)
 * @method static \QuantaForge\Contracts\Process\ProcessResult run(array|string|null $command = null, callable|null $output = null)
 * @method static \QuantaForge\Process\InvokedProcess start(array|string|null $command = null, callable $output = null)
 * @method static \QuantaForge\Process\PendingProcess withFakeHandlers(array $fakeHandlers)
 * @method static \QuantaForge\Process\PendingProcess|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \QuantaForge\Process\PendingProcess|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \QuantaForge\Process\FakeProcessResult result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
 * @method static \QuantaForge\Process\FakeProcessDescription describe()
 * @method static \QuantaForge\Process\FakeProcessSequence sequence(array $processes = [])
 * @method static bool isRecording()
 * @method static \QuantaForge\Process\Factory recordIfRecording(\QuantaForge\Process\PendingProcess $process, \QuantaForge\Contracts\Process\ProcessResult $result)
 * @method static \QuantaForge\Process\Factory record(\QuantaForge\Process\PendingProcess $process, \QuantaForge\Contracts\Process\ProcessResult $result)
 * @method static \QuantaForge\Process\Factory preventStrayProcesses(bool $prevent = true)
 * @method static bool preventingStrayProcesses()
 * @method static \QuantaForge\Process\Factory assertRan(\Closure|string $callback)
 * @method static \QuantaForge\Process\Factory assertRanTimes(\Closure|string $callback, int $times = 1)
 * @method static \QuantaForge\Process\Factory assertNotRan(\Closure|string $callback)
 * @method static \QuantaForge\Process\Factory assertDidntRun(\Closure|string $callback)
 * @method static \QuantaForge\Process\Factory assertNothingRan()
 * @method static \QuantaForge\Process\Pool pool(callable $callback)
 * @method static \QuantaForge\Contracts\Process\ProcessResult pipe(callable|array $callback, callable|null $output = null)
 * @method static \QuantaForge\Process\ProcessPoolResults concurrently(callable $callback, callable|null $output = null)
 * @method static \QuantaForge\Process\PendingProcess newPendingProcess()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \QuantaForge\Process\PendingProcess
 * @see \QuantaForge\Process\Factory
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
     * @return \QuantaForge\Process\Factory
     */
    public static function fake(Closure|array $callback = null)
    {
        return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
