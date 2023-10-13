<?php

namespace QuantaQuirk\Tests\Queue;

use Exception;
use QuantaQuirk\Contracts\Queue\Job;
use QuantaQuirk\Queue\InteractsWithQueue;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class InteractsWithQueueTest extends TestCase
{
    public function testCreatesAnExceptionFromString()
    {
        $queueJob = m::mock(Job::class);
        $queueJob->shouldReceive('fail')->withArgs(function ($e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Whoops!', $e->getMessage());

            return true;
        });

        $job = new class
        {
            use InteractsWithQueue;

            public $job;
        };

        $job->job = $queueJob;
        $job->fail('Whoops!');
    }
}
