<?php

namespace QuantaQuirk\Tests\Integration\Mail;

use QuantaQuirk\Mail\Mailable;
use QuantaQuirk\Mail\SendQueuedMailable;
use QuantaQuirk\Queue\Middleware\RateLimited;
use QuantaQuirk\Support\Facades\Mail;
use QuantaQuirk\Support\Facades\Queue;
use QuantaQuirk\Support\Facades\View;
use Orchestra\Testbench\TestCase;

class SendingQueuedMailTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mail.driver', 'array');

        View::addLocation(__DIR__.'/Fixtures');
    }

    public function testMailIsSentWithDefaultLocale()
    {
        Queue::fake();

        Mail::to('test@mail.com')->queue(new SendingQueuedMailTestMail);

        Queue::assertPushed(SendQueuedMailable::class, function ($job) {
            return $job->middleware[0] instanceof RateLimited;
        });
    }
}

class SendingQueuedMailTestMail extends Mailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view');
    }

    public function middleware()
    {
        return [new RateLimited('limiter')];
    }
}
