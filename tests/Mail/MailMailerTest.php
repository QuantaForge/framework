<?php

namespace QuantaQuirk\Tests\Mail;

use QuantaQuirk\Contracts\Events\Dispatcher;
use QuantaQuirk\Contracts\View\Factory;
use QuantaQuirk\Mail\Events\MessageSending;
use QuantaQuirk\Mail\Events\MessageSent;
use QuantaQuirk\Mail\Mailer;
use QuantaQuirk\Mail\Message;
use QuantaQuirk\Mail\Transport\ArrayTransport;
use QuantaQuirk\Support\HtmlString;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class MailMailerTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['__mailer.test']);

        m::close();
    }

    public function testMailerSendSendsMessageWithProperViewContent()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $this->assertStringContainsString('rendered.view', $sentMessage->toString());
    }

    public function testMailerSendSendsMessageWithCcAndBccRecipients()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')
                ->cc('dries@quantaquirk.com')
                ->bcc('james@quantaquirk.com')
                ->from('hello@quantaquirk.com');
        });

        $recipients = collect($sentMessage->getEnvelope()->getRecipients())->map(function ($recipient) {
            return $recipient->getAddress();
        });

        $this->assertStringContainsString('rendered.view', $sentMessage->toString());
        $this->assertStringContainsString('dries@quantaquirk.com', $sentMessage->toString());
        $this->assertStringNotContainsString('james@quantaquirk.com', $sentMessage->toString());
        $this->assertTrue($recipients->contains('james@quantaquirk.com'));
    }

    public function testMailerSendSendsMessageWithProperViewContentUsingHtmlStrings()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('render')->never();

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send(
            ['html' => new HtmlString('<p>Hello QuantaQuirk</p>'), 'text' => new HtmlString('Hello World')],
            ['data'],
            function (Message $message) {
                $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
            }
        );

        $this->assertStringContainsString('<p>Hello QuantaQuirk</p>', $sentMessage->toString());
        $this->assertStringContainsString('Hello World', $sentMessage->toString());
    }

    public function testMailerSendSendsMessageWithProperViewContentUsingStringCallbacks()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('render')->never();

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send(
            [
                'html' => function ($data) {
                    $this->assertInstanceOf(Message::class, $data['message']);

                    return new HtmlString('<p>Hello QuantaQuirk</p>');
                },
                'text' => function ($data) {
                    $this->assertInstanceOf(Message::class, $data['message']);

                    return new HtmlString('Hello World');
                },
            ],
            [],
            function (Message $message) {
                $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
            }
        );

        $this->assertStringContainsString('<p>Hello QuantaQuirk</p>', $sentMessage->toString());
        $this->assertStringContainsString('Hello World', $sentMessage->toString());
    }

    public function testMailerSendSendsMessageWithProperViewContentUsingHtmlMethod()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('render')->never();

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->html('<p>Hello World</p>', function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $this->assertStringContainsString('<p>Hello World</p>', $sentMessage->toString());
    }

    public function testMailerSendSendsMessageWithProperPlainViewContent()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->twice()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $view->shouldReceive('render')->once()->andReturn('rendered.plain');

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send(['foo', 'bar'], ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $expected = <<<Text
        Content-Type: text/html; charset=utf-8\r
        Content-Transfer-Encoding: quoted-printable\r
        \r
        rendered.view
        Text;

        $this->assertStringContainsString($expected, $sentMessage->toString());

        $expected = <<<Text
        Content-Type: text/plain; charset=utf-8\r
        Content-Transfer-Encoding: quoted-printable\r
        \r
        rendered.plain
        Text;

        $this->assertStringContainsString($expected, $sentMessage->toString());
    }

    public function testMailerSendSendsMessageWithProperPlainViewContentWhenExplicit()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->twice()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $view->shouldReceive('render')->once()->andReturn('rendered.plain');

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->send(['html' => 'foo', 'text' => 'bar'], ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $expected = <<<Text
        Content-Type: text/html; charset=utf-8\r
        Content-Transfer-Encoding: quoted-printable\r
        \r
        rendered.view
        Text;

        $this->assertStringContainsString($expected, $sentMessage->toString());

        $expected = <<<Text
        Content-Type: text/plain; charset=utf-8\r
        Content-Transfer-Encoding: quoted-printable\r
        \r
        rendered.plain
        Text;

        $this->assertStringContainsString($expected, $sentMessage->toString());
    }

    public function testToAllowsEmailAndName()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $mailer = new Mailer('array', $view, new ArrayTransport);

        $sentMessage = $mailer->to('taylor@quantaquirk.com', 'Taylor Otwell')->send(new TestMail());

        $recipients = $sentMessage->getEnvelope()->getRecipients();
        $this->assertCount(1, $recipients);
        $this->assertSame('taylor@quantaquirk.com', $recipients[0]->getAddress());
        $this->assertSame('Taylor Otwell', $recipients[0]->getName());
    }

    public function testGlobalFromIsRespectedOnAllMessages()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $mailer = new Mailer('array', $view, new ArrayTransport);
        $mailer->alwaysFrom('hello@quantaquirk.com');

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com');
        });

        $this->assertSame('taylor@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertSame('hello@quantaquirk.com', $sentMessage->getEnvelope()->getSender()->getAddress());
    }

    public function testGlobalReplyToIsRespectedOnAllMessages()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $mailer = new Mailer('array', $view, new ArrayTransport);
        $mailer->alwaysReplyTo('taylor@quantaquirk.com', 'Taylor Otwell');

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('dries@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $this->assertSame('dries@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertStringContainsString('Reply-To: Taylor Otwell <taylor@quantaquirk.com>', $sentMessage->toString());
    }

    public function testGlobalToIsRespectedOnAllMessages()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');
        $mailer = new Mailer('array', $view, new ArrayTransport);
        $mailer->alwaysTo('taylor@quantaquirk.com', 'Taylor Otwell');

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->from('hello@quantaquirk.com');
            $message->to('nuno@quantaquirk.com');
            $message->cc('dries@quantaquirk.com');
            $message->bcc('james@quantaquirk.com');
        });

        $recipients = collect($sentMessage->getEnvelope()->getRecipients())->map(function ($recipient) {
            return $recipient->getAddress();
        });

        $this->assertSame('taylor@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertDoesNotMatchRegularExpression('/^To: nuno@quantaquirk.com/m', $sentMessage->toString());
        $this->assertDoesNotMatchRegularExpression('/^Cc: dries@quantaquirk.com/m', $sentMessage->toString());
        $this->assertMatchesRegularExpression('/^X-To: nuno@quantaquirk.com/m', $sentMessage->toString());
        $this->assertMatchesRegularExpression('/^X-Cc: dries@quantaquirk.com/m', $sentMessage->toString());
        $this->assertMatchesRegularExpression('/^X-Bcc: james@quantaquirk.com/m', $sentMessage->toString());
        $this->assertFalse($recipients->contains('nuno@quantaquirk.com'));
        $this->assertFalse($recipients->contains('dries@quantaquirk.com'));
        $this->assertFalse($recipients->contains('james@quantaquirk.com'));
    }

    public function testGlobalReturnPathIsRespectedOnAllMessages()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');

        $mailer = new Mailer('array', $view, new ArrayTransport);
        $mailer->alwaysReturnPath('taylorotwell@gmail.com');

        $sentMessage = $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });

        $this->assertStringContainsString('Return-Path: <taylorotwell@gmail.com>', $sentMessage->toString());
    }

    public function testEventsAreDispatched()
    {
        $view = m::mock(Factory::class);
        $view->shouldReceive('make')->once()->andReturn($view);
        $view->shouldReceive('render')->once()->andReturn('rendered.view');

        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('until')->once()->with(m::type(MessageSending::class));
        $events->shouldReceive('dispatch')->once()->with(m::type(MessageSent::class));

        $mailer = new Mailer('array', $view, new ArrayTransport, $events);

        $mailer->send('foo', ['data'], function (Message $message) {
            $message->to('taylor@quantaquirk.com')->from('hello@quantaquirk.com');
        });
    }

    public function testMacroable()
    {
        Mailer::macro('foo', function () {
            return 'bar';
        });

        $mailer = new Mailer('array', m::mock(Factory::class), new ArrayTransport);

        $this->assertSame(
            'bar', $mailer->foo()
        );
    }
}

class TestMail extends \QuantaQuirk\Mail\Mailable
{
    public function build()
    {
        return $this->view('view')
            ->from('hello@quantaquirk.com');
    }
}
