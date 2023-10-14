<?php

namespace QuantaForge\Tests\Integration\Mail;

use QuantaForge\Mail\Mailable;
use QuantaForge\Mail\SendQueuedMailable;
use QuantaForge\Queue\Middleware\RateLimited;
use QuantaForge\Support\Facades\Mail;
use QuantaForge\Support\Facades\Queue;
use QuantaForge\Support\Facades\View;
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
