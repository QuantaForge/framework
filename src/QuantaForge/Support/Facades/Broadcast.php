<?php

namespace QuantaForge\Support\Facades;

use QuantaForge\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static void routes(array|null $attributes = null)
 * @method static void userRoutes(array|null $attributes = null)
 * @method static void channelRoutes(array|null $attributes = null)
 * @method static string|null socket(\QuantaForge\Http\Request|null $request = null)
 * @method static \QuantaForge\Broadcasting\PendingBroadcast event(mixed|null $event = null)
 * @method static void queue(mixed $event)
 * @method static mixed connection(string|null $driver = null)
 * @method static mixed driver(string|null $name = null)
 * @method static \Pusher\Pusher pusher(array $config)
 * @method static \Ably\AblyRest ably(array $config)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static void purge(string|null $name = null)
 * @method static \QuantaForge\Broadcasting\BroadcastManager extend(string $driver, \Closure $callback)
 * @method static \QuantaForge\Contracts\Foundation\Application getApplication()
 * @method static \QuantaForge\Broadcasting\BroadcastManager setApplication(\QuantaForge\Contracts\Foundation\Application $app)
 * @method static \QuantaForge\Broadcasting\BroadcastManager forgetDrivers()
 * @method static mixed auth(\QuantaForge\Http\Request $request)
 * @method static mixed validAuthenticationResponse(\QuantaForge\Http\Request $request, mixed $result)
 * @method static void broadcast(array $channels, string $event, array $payload = [])
 * @method static array|null resolveAuthenticatedUser(\QuantaForge\Http\Request $request)
 * @method static void resolveAuthenticatedUserUsing(\Closure $callback)
 * @method static \QuantaForge\Broadcasting\Broadcasters\Broadcaster channel(\QuantaForge\Contracts\Broadcasting\HasBroadcastChannel|string $channel, callable|string $callback, array $options = [])
 * @method static \QuantaForge\Support\Collection getChannels()
 *
 * @see \QuantaForge\Broadcasting\BroadcastManager
 * @see \QuantaForge\Broadcasting\Broadcasters\Broadcaster
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
