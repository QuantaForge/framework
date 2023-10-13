<?php

namespace QuantaQuirk\Cache\Console;

use QuantaQuirk\Cache\CacheManager;
use QuantaQuirk\Cache\RedisStore;
use QuantaQuirk\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cache:prune-stale-tags')]
class PruneStaleTagsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cache:prune-stale-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale cache tags from the cache (Redis only)';

    /**
     * Execute the console command.
     *
     * @param  \QuantaQuirk\Cache\CacheManager  $cache
     * @return void
     */
    public function handle(CacheManager $cache)
    {
        $cache = $cache->store($this->argument('store'));

        if (! $cache->getStore() instanceof RedisStore) {
            $this->components->error('Pruning cache tags is only necessary when using Redis.');

            return 1;
        }

        $cache->flushStaleTags();

        $this->components->info('Stale cache tags pruned successfully.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['store', InputArgument::OPTIONAL, 'The name of the store you would like to prune tags from'],
        ];
    }
}
