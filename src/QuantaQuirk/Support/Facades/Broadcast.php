<?php

namespace QuantaQuirk\Support\Facades;

use QuantaQuirk\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static void routes(array|null $attributes = null)
 * @method static void userRoutes(array|null $attributes = null)
 * @method static void channelRoutes(array|null $attributes = null)
 * @method static string|null socket(\QuantaQuirk\Http\Request|null $request = null)
 * @method static \QuantaQuirk\Broadcasting\PendingBroadcast event(mixed|null $event = null)
 * @method static void queue(mixed $event)
 * @method static mixed connection(string|null $driver = null)
 * @method static mixed driver(string|null $name = null)
 * @method static \Pusher\Pusher pusher(array $config)
 * @method static \Ably\AblyRest ably(array $config)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static void purge(string|null $name = null)
 * @method static \QuantaQuirk\Broadcasting\BroadcastManager extend(string $driver, \Closure $callback)
 * @method static \QuantaQuirk\Contracts\Foundation\Application getApplication()
 * @method static \QuantaQuirk\Broadcasting\BroadcastManager setApplication(\QuantaQuirk\Contracts\Foundation\Application $app)
 * @method static \QuantaQuirk\Broadcasting\BroadcastManager forgetDrivers()
 * @method static mixed auth(\QuantaQuirk\Http\Request $request)
 * @method static mixed validAuthenticationResponse(\QuantaQuirk\Http\Request $request, mixed $result)
 * @method static void broadcast(array $channels, string $event, array $payload = [])
 * @method static array|null resolveAuthenticatedUser(\QuantaQuirk\Http\Request $request)
 * @method static void resolveAuthenticatedUserUsing(\Closure $callback)
 * @method static \QuantaQuirk\Broadcasting\Broadcasters\Broadcaster channel(\QuantaQuirk\Contracts\Broadcasting\HasBroadcastChannel|string $channel, callable|string $callback, array $options = [])
 * @method static \QuantaQuirk\Support\Collection getChannels()
 *
 * @see \QuantaQuirk\Broadcasting\BroadcastManager
 * @see \QuantaQuirk\Broadcasting\Broadcasters\Broadcaster
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
