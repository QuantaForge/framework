<?php

namespace QuantaQuirk\Support;

class DefaultProviders
{
    /**
     * The current providers.
     *
     * @var array
     */
    protected $providers;

    /**
     * Create a new default provider collection.
     *
     * @return void
     */
    public function __construct(?array $providers = null)
    {
        $this->providers = $providers ?: [
            \QuantaQuirk\Auth\AuthServiceProvider::class,
            \QuantaQuirk\Broadcasting\BroadcastServiceProvider::class,
            \QuantaQuirk\Bus\BusServiceProvider::class,
            \QuantaQuirk\Cache\CacheServiceProvider::class,
            \QuantaQuirk\Foundation\Providers\ConsoleSupportServiceProvider::class,
            \QuantaQuirk\Cookie\CookieServiceProvider::class,
            \QuantaQuirk\Database\DatabaseServiceProvider::class,
            \QuantaQuirk\Encryption\EncryptionServiceProvider::class,
            \QuantaQuirk\Filesystem\FilesystemServiceProvider::class,
            \QuantaQuirk\Foundation\Providers\FoundationServiceProvider::class,
            \QuantaQuirk\Hashing\HashServiceProvider::class,
            \QuantaQuirk\Mail\MailServiceProvider::class,
            \QuantaQuirk\Notifications\NotificationServiceProvider::class,
            \QuantaQuirk\Pagination\PaginationServiceProvider::class,
            \QuantaQuirk\Pipeline\PipelineServiceProvider::class,
            \QuantaQuirk\Queue\QueueServiceProvider::class,
            \QuantaQuirk\Redis\RedisServiceProvider::class,
            \QuantaQuirk\Auth\Passwords\PasswordResetServiceProvider::class,
            \QuantaQuirk\Session\SessionServiceProvider::class,
            \QuantaQuirk\Translation\TranslationServiceProvider::class,
            \QuantaQuirk\Validation\ValidationServiceProvider::class,
            \QuantaQuirk\View\ViewServiceProvider::class,
        ];
    }

    /**
     * Merge the given providers into the provider collection.
     *
     * @param  array  $providers
     * @return static
     */
    public function merge(array $providers)
    {
        $this->providers = array_merge($this->providers, $providers);

        return new static($this->providers);
    }

    /**
     * Replace the given providers with other providers.
     *
     * @param  array  $items
     * @return static
     */
    public function replace(array $replacements)
    {
        $current = collect($this->providers);

        foreach ($replacements as $from => $to) {
            $key = $current->search($from);

            $current = $key ? $current->replace([$key => $to]) : $current;
        }

        return new static($current->values()->toArray());
    }

    /**
     * Disable the given providers.
     *
     * @param  array  $providers
     * @return static
     */
    public function except(array $providers)
    {
        return new static(collect($this->providers)
                ->reject(fn ($p) => in_array($p, $providers))
                ->values()
                ->toArray());
    }

    /**
     * Convert the provider collection to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->providers;
    }
}
