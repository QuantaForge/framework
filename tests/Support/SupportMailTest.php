<?php

namespace QuantaForge\Tests\Support;

use QuantaForge\Mail\Mailable;
use QuantaForge\Support\Facades\Mail;
use Orchestra\Testbench\TestCase;

class SupportMailTest extends TestCase
{
    public function testItRegisterAndCallMacros()
    {
        Mail::macro('test', fn (string $str) => $str === 'foo'
            ? 'it works!'
            : 'it failed.',
        );

        $this->assertEquals('it works!', Mail::test('foo'));
    }

    public function testItRegisterAndCallMacrosWhenFaked()
    {
        Mail::macro('test', fn (string $str) => $str === 'foo'
            ? 'it works!'
            : 'it failed.',
        );

        Mail::fake();

        $this->assertEquals('it works!', Mail::test('foo'));
    }

    public function testEmailSent()
    {
        Mail::fake();
        Mail::assertNothingSent();

        Mail::to('hello@quantaforge.com')->send(new TestMail());

        Mail::assertSent(TestMail::class);
    }
}

class TestMail extends Mailable
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
}
