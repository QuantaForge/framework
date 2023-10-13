<?php

namespace QuantaQuirk\Tests\Queue;

use QuantaQuirk\Bus\BatchRepository;
use QuantaQuirk\Bus\DatabaseBatchRepository;
use QuantaQuirk\Foundation\Application;
use QuantaQuirk\Queue\Console\PruneBatchesCommand;
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
        $command->setQuantaQuirk($container);

        $command->run(new ArrayInput(['--unfinished' => 0]), new NullOutput());

        $repo->shouldHaveReceived('pruneUnfinished')->once();
    }

    public function testAllowPruningAllCancelledBatches()
    {
        $container = new Application;
        $container->instance(BatchRepository::class, $repo = m::spy(DatabaseBatchRepository::class));

        $command = new PruneBatchesCommand;
        $command->setQuantaQuirk($container);

        $command->run(new ArrayInput(['--cancelled' => 0]), new NullOutput());

        $repo->shouldHaveReceived('pruneCancelled')->once();
    }
}
