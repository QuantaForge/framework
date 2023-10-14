<?php

namespace QuantaForge\Support\Facades;

/**
 * @method static \Psr\Log\LoggerInterface build(array $config)
 * @method static \Psr\Log\LoggerInterface stack(array $channels, string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface channel(string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface driver(string|null $driver = null)
 * @method static \QuantaForge\Log\LogManager shareContext(array $context)
 * @method static array sharedContext()
 * @method static \QuantaForge\Log\LogManager flushSharedContext()
 * @method static string|null getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static \QuantaForge\Log\LogManager extend(string $driver, \Closure $callback)
 * @method static void forgetChannel(string|null $driver = null)
 * @method static array getChannels()
 * @method static void emergency(string $message, array $context = [])
 * @method static void alert(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void log(mixed $level, string $message, array $context = [])
 * @method static void write(string $level, \QuantaForge\Contracts\Support\Arrayable|\QuantaForge\Contracts\Support\Jsonable|\QuantaForge\Support\Stringable|array|string $message, array $context = [])
 * @method static \QuantaForge\Log\Logger withContext(array $context = [])
 * @method static \QuantaForge\Log\Logger withoutContext()
 * @method static void listen(\Closure $callback)
 * @method static \Psr\Log\LoggerInterface getLogger()
 * @method static \QuantaForge\Contracts\Events\Dispatcher getEventDispatcher()
 * @method static void setEventDispatcher(\QuantaForge\Contracts\Events\Dispatcher $dispatcher)
 * @method static \QuantaForge\Log\Logger|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \QuantaForge\Log\Logger|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 *
 * @see \QuantaForge\Log\LogManager
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
