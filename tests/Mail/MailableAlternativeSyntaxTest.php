<?php

namespace QuantaQuirk\Tests\Mail;

use QuantaQuirk\Mail\Mailable;
use QuantaQuirk\Mail\Mailables\Address;
use QuantaQuirk\Mail\Mailables\Content;
use QuantaQuirk\Mail\Mailables\Envelope;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MailableAlternativeSyntaxTest extends TestCase
{
    public function testBasicMailableInspection()
    {
        $mailable = new MailableWithAlternativeSyntax;

        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $this->assertTrue($mailable->hasCc('adam@quantaquirk.com'));
        $this->assertTrue($mailable->hasBcc('tyler@quantaquirk.com'));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertFalse($mailable->hasTo('taylor@quantaquirk.com', 'Wrong Name'));

        $mailable->to(new Address('abigail@quantaquirk.com', 'Abigail Otwell'));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));

        $this->assertTrue($mailable->hasSubject('Test Subject'));
        $this->assertFalse($mailable->hasSubject('Wrong Subject'));
        $this->assertTrue($mailable->hasTag('tag-1'));
        $this->assertTrue($mailable->hasMetadata('test-meta', 'test-meta-value'));

        $reflection = new ReflectionClass($mailable);
        $method = $reflection->getMethod('prepareMailableForDelivery');
        $method->invoke($mailable);

        $this->assertEquals('test-view', $mailable->view);
        $this->assertEquals(['test-data-key' => 'test-data-value'], $mailable->viewData);
        $this->assertEquals(2, count($mailable->to));
        $this->assertEquals(1, count($mailable->cc));
        $this->assertEquals(1, count($mailable->bcc));
    }

    public function testEnvelopesCanReceiveAdditionalRecipients()
    {
        $envelope = new Envelope(to: ['taylor@example.com']);
        $envelope->to(new Address('taylorotwell@example.com'));

        $this->assertCount(2, $envelope->to);
        $this->assertEquals('taylor@example.com', $envelope->to[0]->address);
        $this->assertEquals('taylorotwell@example.com', $envelope->to[1]->address);

        $envelope->to('abigailotwell@example.com', 'Abigail Otwell');
        $this->assertEquals('abigailotwell@example.com', $envelope->to[2]->address);
        $this->assertEquals('Abigail Otwell', $envelope->to[2]->name);

        $envelope->to('adam@example.com');
        $this->assertEquals('adam@example.com', $envelope->to[3]->address);
        $this->assertNull($envelope->to[3]->name);

        $envelope->to(['jeffrey@example.com', 'tyler@example.com']);
        $this->assertEquals('jeffrey@example.com', $envelope->to[4]->address);
        $this->assertEquals('tyler@example.com', $envelope->to[5]->address);

        $envelope->from('dries@example.com', 'Dries Vints');
        $this->assertEquals('dries@example.com', $envelope->from->address);
        $this->assertEquals('Dries Vints', $envelope->from->name);
    }
}

class MailableWithAlternativeSyntax extends Mailable
{
    public function envelope()
    {
        return new Envelope(
            to: [new Address('taylor@quantaquirk.com', 'Taylor Otwell')],
            cc: [new Address('adam@quantaquirk.com', 'Adam Wathan')],
            bcc: [new Address('tyler@quantaquirk.com', 'Tyler Blair')],
            subject: 'Test Subject',
            tags: ['tag-1', 'tag-2'],
            metadata: ['test-meta' => 'test-meta-value'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'test-view',
            with: ['test-data-key' => 'test-data-value'],
        );
    }
}
