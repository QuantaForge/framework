<?php

namespace QuantaForge\Tests\Queue;

use QuantaForge\Bus\BatchRepository;
use QuantaForge\Bus\DatabaseBatchRepository;
use QuantaForge\Foundation\Application;
use QuantaForge\Queue\Console\PruneBatchesCommand;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class PruneBatchesCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testAllowPruningAllUnfinishedBatches()
    {
        $container = new Application;
        $container->instance(BatchRepository::class, $repo = m::spy(DatabaseBatchRepository::class));

        $command = new PruneBatchesCommand;
        $command->setQuantaForge($container);

        $command->run(new ArrayInput(['--unfinished' => 0]), new NullOutput());

        $repo->shouldHaveReceived('pruneUnfinished')->once();
    }

    public function testAllowPruningAllCancelledBatches()
    {
        $container = new Application;
        $container->instance(BatchRepository::class, $repo = m::spy(DatabaseBatchRepository::class));

        $command = new PruneBatchesCommand;
        $command->setQuantaForge($container);

        $command->run(new ArrayInput(['--cancelled' => 0]), new NullOutput());

        $repo->shouldHaveReceived('pruneCancelled')->once();
    }
}
