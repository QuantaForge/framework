<?php

namespace QuantaQuirk\Tests\Integration\Queue;

use QuantaQuirk\Contracts\Bus\QueueingDispatcher;
use QuantaQuirk\Contracts\Queue\ShouldQueue;
use QuantaQuirk\Foundation\Testing\TestCase;
use QuantaQuirk\Queue\Queue;
use QuantaQuirk\Support\ServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;

class CustomPayloadTest extends TestCase
{
    use CreatesApplication;

    protected function getPackageProviders($app)
    {
        return [QueueServiceProvider::class];
    }

    public static function websites()
    {
        yield ['quantaquirk.com'];

        yield ['blog.quantaquirk.com'];
    }

    /**
     * @dataProvider websites
     */
    public function test_custom_payload_gets_cleared_for_each_data_provider(string $websites)
    {
        $dispatcher = $this->app->make(QueueingDispatcher::class);

        $dispatcher->dispatchToQueue(new MyJob);
    }
}

class QueueServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('one.time.password', function () {
            return random_int(1, 10);
        });

        Queue::createPayloadUsing(function () {
            $password = $this->app->make('one.time.password');

            $this->app->offsetUnset('one.time.password');

            return ['password' => $password];
        });
    }
}

class MyJob implements ShouldQueue
{
    public $connection = 'sync';

    public function handle()
    {
        //
    }
}
