<?php

namespace QuantaQuirk\Support\Facades;

use QuantaQuirk\Bus\BatchRepository;
use QuantaQuirk\Contracts\Bus\Dispatcher as BusDispatcherContract;
use QuantaQuirk\Foundation\Bus\PendingChain;
use QuantaQuirk\Support\Testing\Fakes\BusFake;

/**
 * @method static mixed dispatch(mixed $command)
 * @method static mixed dispatchSync(mixed $command, mixed $handler = null)
 * @method static mixed dispatchNow(mixed $command, mixed $handler = null)
 * @method static \QuantaQuirk\Bus\Batch|null findBatch(string $batchId)
 * @method static \QuantaQuirk\Bus\PendingBatch batch(\QuantaQuirk\Support\Collection|array|mixed $jobs)
 * @method static \QuantaQuirk\Foundation\Bus\PendingChain chain(\QuantaQuirk\Support\Collection|array $jobs)
 * @method static bool hasCommandHandler(mixed $command)
 * @method static bool|mixed getCommandHandler(mixed $command)
 * @method static mixed dispatchToQueue(mixed $command)
 * @method static void dispatchAfterResponse(mixed $command, mixed $handler = null)
 * @method static \QuantaQuirk\Bus\Dispatcher pipeThrough(array $pipes)
 * @method static \QuantaQuirk\Bus\Dispatcher map(array $map)
 * @method static \QuantaQuirk\Support\Testing\Fakes\BusFake except(array|string $jobsToDispatch)
 * @method static void assertDispatched(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatched(string|\Closure $command, callable|null $callback = null)
 * @method static void assertNothingDispatched()
 * @method static void assertDispatchedSync(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedSyncTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatchedSync(string|\Closure $command, callable|null $callback = null)
 * @method static void assertDispatchedAfterResponse(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedAfterResponseTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatchedAfterResponse(string|\Closure $command, callable|null $callback = null)
 * @method static void assertChained(array $expectedChain)
 * @method static void assertDispatchedWithoutChain(string|\Closure $command, callable|null $callback = null)
 * @method static void assertBatched(callable $callback)
 * @method static void assertBatchCount(int $count)
 * @method static void assertNothingBatched()
 * @method static \QuantaQuirk\Support\Collection dispatched(string $command, callable|null $callback = null)
 * @method static \QuantaQuirk\Support\Collection dispatchedSync(string $command, callable|null $callback = null)
 * @method static \QuantaQuirk\Support\Collection dispatchedAfterResponse(string $command, callable|null $callback = null)
 * @method static \QuantaQuirk\Support\Collection batched(callable $callback)
 * @method static bool hasDispatched(string $command)
 * @method static bool hasDispatchedSync(string $command)
 * @method static bool hasDispatchedAfterResponse(string $command)
 * @method static \QuantaQuirk\Bus\Batch dispatchFakeBatch(string $name = '')
 * @method static \QuantaQuirk\Bus\Batch recordPendingBatch(\QuantaQuirk\Bus\PendingBatch $pendingBatch)
 * @method static \QuantaQuirk\Support\Testing\Fakes\BusFake serializeAndRestore(bool $serializeAndRestore = true)
 *
 * @see \QuantaQuirk\Bus\Dispatcher
 * @see \QuantaQuirk\Support\Testing\Fakes\BusFake
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $jobsToFake
     * @param  \QuantaQuirk\Bus\BatchRepository|null  $batchRepository
     * @return \QuantaQuirk\Support\Testing\Fakes\BusFake
     */
    public static function fake($jobsToFake = [], BatchRepository $batchRepository = null)
    {
        $actualDispatcher = static::isFake()
                ? static::getFacadeRoot()->dispatcher
                : static::getFacadeRoot();

        return tap(new BusFake($actualDispatcher, $jobsToFake, $batchRepository), function ($fake) {
            static::swap($fake);
        });
    }

    /**
     * Dispatch the given chain of jobs.
     *
     * @param  array|mixed  $jobs
     * @return \QuantaQuirk\Foundation\Bus\PendingDispatch
     */
    public static function dispatchChain($jobs)
    {
        $jobs = is_array($jobs) ? $jobs : func_get_args();

        return (new PendingChain(array_shift($jobs), $jobs))
                    ->dispatch();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}
