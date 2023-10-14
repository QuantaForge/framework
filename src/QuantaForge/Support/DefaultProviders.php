<?php

namespace QuantaForge\Support;

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
            \QuantaForge\Auth\AuthServiceProvider::class,
            \QuantaForge\Broadcasting\BroadcastServiceProvider::class,
            \QuantaForge\Bus\BusServiceProvider::class,
            \QuantaForge\Cache\CacheServiceProvider::class,
            \QuantaForge\Foundation\Providers\ConsoleSupportServiceProvider::class,
            \QuantaForge\Cookie\CookieServiceProvider::class,
            \QuantaForge\Database\DatabaseServiceProvider::class,
            \QuantaForge\Encryption\EncryptionServiceProvider::class,
            \QuantaForge\Filesystem\FilesystemServiceProvider::class,
            \QuantaForge\Foundation\Providers\FoundationServiceProvider::class,
            \QuantaForge\Hashing\HashServiceProvider::class,
            \QuantaForge\Mail\MailServiceProvider::class,
            \QuantaForge\Notifications\NotificationServiceProvider::class,
            \QuantaForge\Pagination\PaginationServiceProvider::class,
            \QuantaForge\Pipeline\PipelineServiceProvider::class,
            \QuantaForge\Queue\QueueServiceProvider::class,
            \QuantaForge\Redis\RedisServiceProvider::class,
            \QuantaForge\Auth\Passwords\PasswordResetServiceProvider::class,
            \QuantaForge\Session\SessionServiceProvider::class,
            \QuantaForge\Translation\TranslationServiceProvider::class,
            \QuantaForge\Validation\ValidationServiceProvider::class,
            \QuantaForge\View\ViewServiceProvider::class,
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
