<?php

namespace QuantaQuirk\Tests\Mail;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Contracts\Mail\Attachable;
use QuantaQuirk\Contracts\View\Factory;
use QuantaQuirk\Mail\Attachment;
use QuantaQuirk\Mail\Mailable;
use QuantaQuirk\Mail\Mailables\Envelope;
use QuantaQuirk\Mail\Mailables\Headers;
use QuantaQuirk\Mail\Mailer;
use QuantaQuirk\Mail\Transport\ArrayTransport;
use Mockery as m;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class MailMailableTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testMailableSetsRecipientsCorrectly()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new WelcomeMailableStub;
        $mailable->to('taylor@quantaquirk.com');
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com');
        $mailable->to('taylor@quantaquirk.com', 'Taylor Otwell');

        // Add the same recipient again, but with a different name. This should set the name correctly.
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertHasTo('taylor@quantaquirk.com', 'Taylor Otwell');

        $mailable = new WelcomeMailableStub;
        $mailable->to('taylor@quantaquirk.com', 'Taylor Otwell');
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->to(['taylor@quantaquirk.com']);
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $this->assertFalse($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertHasTo('taylor@quantaquirk.com');
        try {
            $mailable->assertHasTo('taylor@quantaquirk.com', 'Taylor Otwell');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected recipient [taylor@quantaquirk.com (Taylor Otwell)] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new WelcomeMailableStub;
        $mailable->to([['name' => 'Taylor Otwell', 'email' => 'taylor@quantaquirk.com']]);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->to(new MailableTestUserStub);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->to(collect([new MailableTestUserStub]));
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->to);
        $this->assertTrue($mailable->hasTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->to(collect([new MailableTestUserStub, new MailableTestUserStub, new MailableTestUserStub2]));
        $this->assertEquals([
            ['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com'],
            ['name' => 'QuantaQuirk Framework', 'address' => 'contact@quantaquirk.com'],
        ], $mailable->to);
        $this->assertTrue($mailable->hasTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasTo('taylor@quantaquirk.com'));
        $mailable->assertHasTo('taylor@quantaquirk.com');

        foreach (['', null, [], false] as $address) {
            $mailable = new WelcomeMailableStub;
            $mailable->to($address);
            $this->assertFalse($mailable->hasTo(new MailableTestUserStub));
            $this->assertFalse($mailable->hasTo($address));
            try {
                $mailable->assertHasTo($address);
                $this->fail();
            } catch (AssertionFailedError $e) {
                if (! is_string($address)) {
                    $address = json_encode($address);
                }
                $this->assertSame("Did not see expected recipient [{$address}] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
            }
        }
    }

    public function testMailableSetsCcRecipientsCorrectly()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new WelcomeMailableStub;
        $mailable->cc('taylor@quantaquirk.com');
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc('taylor@quantaquirk.com', 'Taylor Otwell');
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc(['taylor@quantaquirk.com']);
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $this->assertFalse($mailable->hasCc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertHasCc('taylor@quantaquirk.com');
        try {
            $mailable->assertHasCc('taylor@quantaquirk.com', 'Taylor Otwell');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected recipient [taylor@quantaquirk.com (Taylor Otwell)] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new WelcomeMailableStub;
        $mailable->cc([['name' => 'Taylor Otwell', 'email' => 'taylor@quantaquirk.com']]);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc(new MailableTestUserStub);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc(collect([new MailableTestUserStub]));
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->cc);
        $this->assertTrue($mailable->hasCc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc(collect([new MailableTestUserStub, new MailableTestUserStub, new MailableTestUserStub2]));
        $this->assertEquals([
            ['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com'],
            ['name' => 'QuantaQuirk Framework', 'address' => 'contact@quantaquirk.com'],
        ], $mailable->cc);
        $this->assertTrue($mailable->hasCc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->cc(['taylor@quantaquirk.com', 'not-taylor@quantaquirk.com']);
        $this->assertEquals([
            ['name' => null, 'address' => 'taylor@quantaquirk.com'],
            ['name' => null, 'address' => 'not-taylor@quantaquirk.com'],
        ], $mailable->cc);
        $this->assertTrue($mailable->hasCc('taylor@quantaquirk.com'));
        $this->assertTrue($mailable->hasCc('not-taylor@quantaquirk.com'));
        $mailable->assertHasCc('taylor@quantaquirk.com');
        $mailable->assertHasCc('not-taylor@quantaquirk.com');

        foreach (['', null, [], false] as $address) {
            $mailable = new WelcomeMailableStub;
            $mailable->cc($address);
            $this->assertFalse($mailable->hasCc(new MailableTestUserStub));
            $this->assertFalse($mailable->hasCc($address));
            try {
                $mailable->assertHasCc($address);
                $this->fail();
            } catch (AssertionFailedError $e) {
                if (! is_string($address)) {
                    $address = json_encode($address);
                }
                $this->assertSame("Did not see expected recipient [{$address}] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
            }
        }
    }

    public function testMailableSetsBccRecipientsCorrectly()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new WelcomeMailableStub;
        $mailable->bcc('taylor@quantaquirk.com');
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc('taylor@quantaquirk.com', 'Taylor Otwell');
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc(['taylor@quantaquirk.com']);
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $this->assertFalse($mailable->hasBcc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');
        try {
            $mailable->assertHasBcc('taylor@quantaquirk.com', 'Taylor Otwell');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected recipient [taylor@quantaquirk.com (Taylor Otwell)] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new WelcomeMailableStub;
        $mailable->bcc([['name' => 'Taylor Otwell', 'email' => 'taylor@quantaquirk.com']]);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc(new MailableTestUserStub);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc(collect([new MailableTestUserStub]));
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc(collect([new MailableTestUserStub, new MailableTestUserStub, new MailableTestUserStub2]));
        $this->assertEquals([
            ['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com'],
            ['name' => 'QuantaQuirk Framework', 'address' => 'contact@quantaquirk.com'],
        ], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc(new MailableTestUserStub));
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->bcc(['taylor@quantaquirk.com', 'not-taylor@quantaquirk.com']);
        $this->assertEquals([
            ['name' => null, 'address' => 'taylor@quantaquirk.com'],
            ['name' => null, 'address' => 'not-taylor@quantaquirk.com'],
        ], $mailable->bcc);
        $this->assertTrue($mailable->hasBcc('taylor@quantaquirk.com'));
        $this->assertTrue($mailable->hasBcc('not-taylor@quantaquirk.com'));
        $mailable->assertHasBcc('taylor@quantaquirk.com');
        $mailable->assertHasBcc('not-taylor@quantaquirk.com');

        foreach (['', null, [], false] as $address) {
            $mailable = new WelcomeMailableStub;
            $mailable->bcc($address);
            $this->assertFalse($mailable->hasBcc(new MailableTestUserStub));
            $this->assertFalse($mailable->hasBcc($address));
            try {
                $mailable->assertHasBcc($address);
                $this->fail();
            } catch (AssertionFailedError $e) {
                if (! is_string($address)) {
                    $address = json_encode($address);
                }
                $this->assertSame("Did not see expected recipient [{$address}] in email recipients.\nFailed asserting that false is true.", $e->getMessage());
            }
        }
    }

    public function testMailableSetsReplyToCorrectly()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo('taylor@quantaquirk.com');
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo('taylor@quantaquirk.com', 'Taylor Otwell');
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo(['taylor@quantaquirk.com']);
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $this->assertFalse($mailable->hasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');
        try {
            $mailable->assertHasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected address [taylor@quantaquirk.com (Taylor Otwell)] as email 'reply to' recipient.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo([['name' => 'Taylor Otwell', 'email' => 'taylor@quantaquirk.com']]);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');
        $mailable->assertHasReplyTo('taylor@quantaquirk.com', 'Taylor Otwell');

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo(new MailableTestUserStub);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo(collect([new MailableTestUserStub]));
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->replyTo(collect([new MailableTestUserStub, new MailableTestUserStub, new MailableTestUserStub2]));
        $this->assertEquals([
            ['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com'],
            ['name' => 'QuantaQuirk Framework', 'address' => 'contact@quantaquirk.com'],
        ], $mailable->replyTo);
        $this->assertTrue($mailable->hasReplyTo(new MailableTestUserStub));
        $this->assertTrue($mailable->hasReplyTo('taylor@quantaquirk.com'));
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');

        foreach (['', null, [], false] as $address) {
            $mailable = new WelcomeMailableStub;
            $mailable->replyTo($address);
            $this->assertFalse($mailable->hasReplyTo(new MailableTestUserStub));
            $this->assertFalse($mailable->hasReplyTo($address));
            try {
                $mailable->assertHasReplyTo($address);
                $this->fail();
            } catch (AssertionFailedError $e) {
                if (! is_string($address)) {
                    $address = json_encode($address);
                }
                $this->assertSame("Did not see expected address [{$address}] as email 'reply to' recipient.\nFailed asserting that false is true.", $e->getMessage());
            }
        }
    }

    public function testMailableSetsFromCorrectly()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new WelcomeMailableStub;
        $mailable->from('taylor@quantaquirk.com');
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->from('taylor@quantaquirk.com', 'Taylor Otwell');
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertFrom('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->from(['taylor@quantaquirk.com']);
        $this->assertEquals([['name' => null, 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $this->assertFalse($mailable->hasFrom('taylor@quantaquirk.com', 'Taylor Otwell'));
        $mailable->assertFrom('taylor@quantaquirk.com');
        try {
            $mailable->assertFrom('taylor@quantaquirk.com', 'Taylor Otwell');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Email was not from expected address [taylor@quantaquirk.com (Taylor Otwell)].\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new WelcomeMailableStub;
        $mailable->from([['name' => 'Taylor Otwell', 'email' => 'taylor@quantaquirk.com']]);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com', 'Taylor Otwell'));
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertFrom('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->from(new MailableTestUserStub);
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom(new MailableTestUserStub));
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->from(collect([new MailableTestUserStub]));
        $this->assertEquals([['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com']], $mailable->from);
        $this->assertTrue($mailable->hasFrom(new MailableTestUserStub));
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com');

        $mailable = new WelcomeMailableStub;
        $mailable->from(collect([new MailableTestUserStub, new MailableTestUserStub, new MailableTestUserStub2]));
        $this->assertEquals([
            ['name' => 'Taylor Otwell', 'address' => 'taylor@quantaquirk.com'],
            ['name' => 'QuantaQuirk Framework', 'address' => 'contact@quantaquirk.com'],
        ], $mailable->from);
        $this->assertTrue($mailable->hasFrom(new MailableTestUserStub));
        $this->assertTrue($mailable->hasFrom('taylor@quantaquirk.com'));
        $mailable->assertFrom('taylor@quantaquirk.com');

        foreach (['', null, [], false] as $address) {
            $mailable = new WelcomeMailableStub;
            $mailable->from($address);
            $this->assertFalse($mailable->hasFrom(new MailableTestUserStub));
            $this->assertFalse($mailable->hasFrom($address));
            try {
                $mailable->assertFrom($address);
                $this->fail();
            } catch (AssertionFailedError $e) {
                if (! is_string($address)) {
                    $address = json_encode($address);
                }
                $this->assertSame("Email was not from expected address [{$address}].\nFailed asserting that false is true.", $e->getMessage());
            }
        }
    }

    public function testMailableSetsSubjectCorrectly()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->subject('foo');
        $this->assertTrue($mailable->hasSubject('foo'));
    }

    public function testItIgnoresDuplicatedRawAttachments()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->attachData('content1', 'report-1.txt');
        $this->assertCount(1, $mailable->rawAttachments);

        $mailable->attachData('content2', 'report-2.txt');
        $this->assertCount(2, $mailable->rawAttachments);

        $mailable->attachData('content1', 'report-1.txt');
        $mailable->attachData('content2', 'report-2.txt');
        $this->assertCount(2, $mailable->rawAttachments);

        $mailable->attachData('content1', 'report-3.txt');
        $mailable->attachData('content2', 'report-4.txt');
        $this->assertCount(4, $mailable->rawAttachments);

        $this->assertSame([
            [
                'data' => 'content1',
                'name' => 'report-1.txt',
                'options' => [],
            ],
            [
                'data' => 'content2',
                'name' => 'report-2.txt',
                'options' => [],
            ],
            [
                'data' => 'content1',
                'name' => 'report-3.txt',
                'options' => [],
            ],
            [
                'data' => 'content2',
                'name' => 'report-4.txt',
                'options' => [],
            ],
        ], $mailable->rawAttachments);
    }

    public function testItIgnoresDuplicateStorageAttachments()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->attachFromStorageDisk('disk1', 'sample/file.txt');
        $this->assertCount(1, $mailable->diskAttachments);

        $mailable->attachFromStorageDisk('disk1', 'sample/file2.txt');
        $this->assertCount(2, $mailable->diskAttachments);

        $mailable->attachFromStorageDisk('disk1', 'sample/file.txt', 'file.txt');
        $mailable->attachFromStorageDisk('disk1', 'sample/file2.txt');
        $this->assertCount(2, $mailable->diskAttachments);

        $mailable->attachFromStorageDisk('disk2', 'sample/file.txt', 'file.txt');
        $mailable->attachFromStorageDisk('disk2', 'sample/file2.txt');
        $this->assertCount(4, $mailable->diskAttachments);

        $mailable->attachFromStorageDisk('disk1', 'sample/file.txt', 'custom.txt');
        $this->assertCount(5, $mailable->diskAttachments);

        $this->assertSame([
            [
                'disk' => 'disk1',
                'path' => 'sample/file.txt',
                'name' => 'file.txt',
                'options' => [],
            ],
            [
                'disk' => 'disk1',
                'path' => 'sample/file2.txt',
                'name' => 'file2.txt',
                'options' => [],
            ],
            [
                'disk' => 'disk2',
                'path' => 'sample/file.txt',
                'name' => 'file.txt',
                'options' => [],
            ],
            [
                'disk' => 'disk2',
                'path' => 'sample/file2.txt',
                'name' => 'file2.txt',
                'options' => [],
            ],
            [
                'disk' => 'disk1',
                'path' => 'sample/file.txt',
                'name' => 'custom.txt',
                'options' => [],
            ],
        ], $mailable->diskAttachments);
    }

    public function testMailableBuildsViewData()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->build();

        $expected = [
            'first_name' => 'Taylor',
            'lastName' => 'Otwell',
            'framework' => 'QuantaQuirk',
        ];

        $this->assertSame($expected, $mailable->buildViewData());
    }

    public function testMailerMayBeSet()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->mailer('array');

        $mailable = unserialize(serialize($mailable));

        $this->assertSame('array', $mailable->mailer);
    }

    public function testMailablePriorityGetsSent()
    {
        $view = m::mock(Factory::class);

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $mailable = new WelcomeMailableStub;
        $mailable->to('hello@quantaquirk.com');
        $mailable->from('taylor@quantaquirk.com');
        $mailable->html('test content');

        $mailable->priority(1);

        $sentMessage = $mailer->send($mailable);

        $this->assertSame('hello@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertStringContainsString('X-Priority: 1 (Highest)', $sentMessage->toString());
    }

    public function testMailableMetadataGetsSent()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $view = m::mock(Factory::class);

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $mailable = new WelcomeMailableStub;
        $mailable->to('hello@quantaquirk.com');
        $mailable->from('taylor@quantaquirk.com');
        $mailable->html('test content');

        $mailable->metadata('origin', 'test-suite');
        $mailable->metadata('user_id', 1);

        $sentMessage = $mailer->send($mailable);

        $this->assertSame('hello@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertStringContainsString('X-Metadata-origin: test-suite', $sentMessage->toString());
        $this->assertStringContainsString('X-Metadata-user_id: 1', $sentMessage->toString());

        $this->assertTrue($mailable->hasMetadata('origin', 'test-suite'));
        $this->assertTrue($mailable->hasMetadata('user_id', 1));
        $this->assertFalse($mailable->hasMetadata('test', 'test'));
        $mailable->assertHasMetadata('origin', 'test-suite');
        $mailable->assertHasMetadata('user_id', 1);
        try {
            $mailable->assertHasMetadata('test', 'test');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected key [test] and value [test] in email metadata.\nFailed asserting that false is true.", $e->getMessage());
        }
    }

    public function testMailableTagGetsSent()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $view = m::mock(Factory::class);

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $mailable = new WelcomeMailableStub;
        $mailable->to('hello@quantaquirk.com');
        $mailable->from('taylor@quantaquirk.com');
        $mailable->html('test content');

        $mailable->tag('test');
        $mailable->tag('foo');

        $sentMessage = $mailer->send($mailable);

        $this->assertSame('hello@quantaquirk.com', $sentMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertStringContainsString('X-Tag: test', $sentMessage->toString());
        $this->assertStringContainsString('X-Tag: foo', $sentMessage->toString());

        $this->assertTrue($mailable->hasTag('test'));
        $this->assertTrue($mailable->hasTag('foo'));
        $this->assertFalse($mailable->hasTag('bar'));
        $mailable->assertHasTag('test');
        $mailable->assertHasTag('foo');
        try {
            $mailable->assertHasTag('bar');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not see expected tag [bar] in email tags.\nFailed asserting that false is true.", $e->getMessage());
        }
    }

    public function testItCanAttachMultipleFiles()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->attachMany([
            '/forge.svg',
            '/vapor.svg' => ['as' => 'Vapor Logo.svg', 'mime' => 'text/css'],
            new class() implements Attachable
            {
                public function toMailAttachment()
                {
                    return Attachment::fromPath('/foo.jpg')->as('bar')->withMime('image/png');
                }
            },
        ]);

        $this->assertCount(3, $mailable->attachments);
        $this->assertSame([
            'file' => '/forge.svg',
            'options' => [],
        ], $mailable->attachments[0]);
        $this->assertSame([
            'file' => '/vapor.svg',
            'options' => [
                'as' => 'Vapor Logo.svg',
                'mime' => 'text/css',
            ],
        ], $mailable->attachments[1]);
        $this->assertSame([
            'file' => '/foo.jpg',
            'options' => [
                'as' => 'bar',
                'mime' => 'image/png',
            ],
        ], $mailable->attachments[2]);
    }

    public function testItAttachesFilesViaAttachableContractFromPath()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->attach(new class() implements Attachable
        {
            public function toMailAttachment()
            {
                return Attachment::fromPath('/foo.jpg')->as('bar')->withMime('image/png');
            }
        });

        $this->assertSame([
            'file' => '/foo.jpg',
            'options' => [
                'as' => 'bar',
                'mime' => 'image/png',
            ],
        ], $mailable->attachments[0]);
    }

    public function testItAttachesFilesViaAttachableContractFromData()
    {
        $mailable = new WelcomeMailableStub;

        $mailable->attach(new class() implements Attachable
        {
            public function toMailAttachment()
            {
                return Attachment::fromData(fn () => 'bar', 'foo.jpg')->withMime('image/png');
            }
        });

        $this->assertSame([
            'data' => 'bar',
            'name' => 'foo.jpg',
            'options' => [
                'mime' => 'image/png',
            ],
        ], $mailable->rawAttachments[0]);
    }

    public function testItCanJitNameAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $unnamedAttachable = new class() implements Attachable
        {
            public function toMailAttachment()
            {
                return Attachment::fromData(fn () => 'bar')->withMime('image/png');
            }
        };

        $mailable->attach($unnamedAttachable, ['as' => 'foo.jpg']);

        $this->assertSame([
            'data' => 'bar',
            'name' => 'foo.jpg',
            'options' => [
                'mime' => 'image/png',
            ],
        ], $mailable->rawAttachments[0]);
    }

    public function testHasAttachmentWithJitNamedAttachment()
    {
        $mailable = new WelcomeMailableStub;
        $unnamedAttachable = new class() implements Attachable
        {
            public function toMailAttachment()
            {
                return Attachment::fromData(fn () => 'bar')->withMime('image/png');
            }
        };

        $mailable->attach($unnamedAttachable, ['as' => 'foo.jpg']);

        $this->assertTrue($mailable->hasAttachment($unnamedAttachable, ['as' => 'foo.jpg']));
    }

    public function testHasAttachmentWithEnvelopeAttachments()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });
        $mailable = new class extends Mailable
        {
            public function envelope()
            {
                return new Envelope();
            }

            public function attachments()
            {
                return [
                    Attachment::fromData(fn () => 'bar')
                        ->withMime('image/png')
                        ->as('foo.jpg'),
                ];
            }
        };
        $unnamedAttachable = new class() implements Attachable
        {
            public function toMailAttachment()
            {
                return Attachment::fromData(fn () => 'bar');
            }
        };

        $mailable->render();

        $this->assertFalse($mailable->hasAttachment($unnamedAttachable));
        $this->assertFalse($mailable->hasAttachment($unnamedAttachable, ['as' => 'foo.jpg']));
        $this->assertFalse($mailable->hasAttachment($unnamedAttachable, ['mime' => 'image/png']));
        $this->assertTrue($mailable->hasAttachment($unnamedAttachable, ['as' => 'foo.jpg', 'mime' => 'image/png']));
    }

    public function testItCanCheckForPathBasedAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->attach('foo.jpg');

        $this->assertTrue($mailable->hasAttachment('foo.jpg'));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('foo.jpg')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('foo.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('foo.jpg')->withMime('text/css')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg')->withMime('text/css'))));

        $mailable = new WelcomeMailableStub;
        $mailable->attach('bar.jpg', ['mime' => 'text/css']);

        $this->assertTrue($mailable->hasAttachment('bar.jpg', ['mime' => 'text/css']));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/css')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/css'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg', ['mime' => 'text/html']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/html')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/html'))));
    }

    public function testItCanCheckForAttachmentBasedAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->attach(Attachment::fromPath('foo.jpg'));

        $this->assertTrue($mailable->hasAttachment('foo.jpg'));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('foo.jpg')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('foo.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('foo.jpg')->withMime('text/css')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg')->withMime('text/css'))));

        $mailable = new WelcomeMailableStub;
        $mailable->attach(Attachment::fromPath('bar.jpg')->withMime('text/css'));

        $this->assertTrue($mailable->hasAttachment('bar.jpg', ['mime' => 'text/css']));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/css')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/css'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg', ['mime' => 'text/html']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/html')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/html'))));
    }

    public function testItCanCheckForAttachableBasedAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->attach(new MailTestAttachable(Attachment::fromPath('foo.jpg')));

        $this->assertTrue($mailable->hasAttachment('foo.jpg'));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('foo.jpg')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('foo.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('foo.jpg')->withMime('text/css')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('foo.jpg')->withMime('text/css'))));

        $mailable = new WelcomeMailableStub;
        $mailable->attach(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/css')));

        $this->assertTrue($mailable->hasAttachment('bar.jpg', ['mime' => 'text/css']));
        $this->assertTrue($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/css')));
        $this->assertTrue($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/css'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg'));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg'))));

        $this->assertFalse($mailable->hasAttachment('bar.jpg', ['mime' => 'text/html']));
        $this->assertFalse($mailable->hasAttachment(Attachment::fromPath('bar.jpg')->withMime('text/html')));
        $this->assertFalse($mailable->hasAttachment(new MailTestAttachable(Attachment::fromPath('bar.jpg')->withMime('text/html'))));
    }

    public function testItCanCheckForDataBasedAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->attachData('data', 'foo.jpg');

        $this->assertTrue($mailable->hasAttachedData('data', 'foo.jpg'));
        $this->assertFalse($mailable->hasAttachedData('xxxx', 'foo.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'foo.jpg', ['mime' => 'text/css']));

        $mailable = new WelcomeMailableStub;
        $mailable->attachData('data', 'bar.jpg', ['mime' => 'text/css']);

        $this->assertTrue($mailable->hasAttachedData('data', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachedData('xxxx', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg', ['mime' => 'text/html']));

        $mailable = new WelcomeMailableStub;
        $mailable->attach(Attachment::fromData(fn () => 'data', 'foo.jpg'));

        $this->assertTrue($mailable->hasAttachedData('data', 'foo.jpg'));
        $this->assertFalse($mailable->hasAttachedData('xxxx', 'foo.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'foo.jpg', ['mime' => 'text/css']));

        $mailable = new WelcomeMailableStub;
        $mailable->attach(Attachment::fromData(fn () => 'data', 'bar.jpg')->withMime('text/css'));

        $this->assertTrue($mailable->hasAttachedData('data', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachedData('xxxx', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachedData('data', 'bar.jpg', ['mime' => 'text/html']));
    }

    public function testItCanCheckForStorageBasedAttachments()
    {
        $mailable = new WelcomeMailableStub;
        $mailable->attachFromStorageDisk('disk', '/path/to/foo.jpg');

        $this->assertTrue($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('xxxx', '/path/to/foo.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', null, ['mime' => 'text/css']));

        $mailable = new WelcomeMailableStub;
        $mailable->attachFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg');

        $this->assertTrue($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('xxxx', '/path/to/foo.jpg', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', 'bar.jpg', 'bar.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'foo.jpg'));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg', ['mime' => 'text/css']));

        $mailable = new WelcomeMailableStub;
        $mailable->attachFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg', ['mime' => 'text/css']);

        $this->assertTrue($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('xxxx', '/path/to/foo.jpg', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', 'bar.jpg', 'bar.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'foo.jpg', ['mime' => 'text/css']));
        $this->assertFalse($mailable->hasAttachmentFromStorageDisk('disk', '/path/to/foo.jpg', 'bar.jpg', ['mime' => 'text/html']));
    }

    public function testAssertHasAttachment()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                //
            }
        };

        try {
            $mailable->assertHasAttachment('/path/to/foo.jpg');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not find the expected attachment.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                $this->attach('/path/to/foo.jpg');
            }
        };

        $mailable->assertHasAttachment('/path/to/foo.jpg');
    }

    public function testAssertHasAttachedData()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                //
            }
        };

        try {
            $mailable->assertHasAttachedData('data', 'foo.jpg');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not find the expected attachment.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                $this->attachData('data', 'foo.jpg');
            }
        };

        $mailable->assertHasAttachedData('data', 'foo.jpg');
    }

    public function testAssertHasAttachmentFromStorage()
    {
        $mailable = new class() extends Mailable
        {
            public function build()
            {
                //
            }
        };

        try {
            $mailable->assertHasAttachmentFromStorage('/path/to/foo.jpg');
            $this->fail();
        } catch (AssertionFailedError $e) {
            $this->assertSame("Did not find the expected attachment.\nFailed asserting that false is true.", $e->getMessage());
        }

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                $this->attachFromStorage('/path/to/foo.jpg');
            }
        };

        $mailable->assertHasAttachmentFromStorage('/path/to/foo.jpg');
    }

    public function testAssertHasSubject()
    {
    }

    public function testMailableHeadersGetSent()
    {
        $view = m::mock(Factory::class);

        $mailer = new Mailer('array', $view, new ArrayTransport);

        $mailable = new MailableHeadersStub;
        $mailable->to('hello@quantaquirk.com');
        $mailable->from('taylor@quantaquirk.com');
        $mailable->html('test content');

        $sentMessage = $mailer->send($mailable);

        $this->assertSame('custom-message-id@example.com', $sentMessage->getMessageId());

        $this->assertTrue($sentMessage->getOriginalMessage()->getHeaders()->has('references'));
        $this->assertEquals('References', $sentMessage->getOriginalMessage()->getHeaders()->get('references')->getName());
        $this->assertEquals('<previous-message@example.com>', $sentMessage->getOriginalMessage()->getHeaders()->get('references')->getValue());

        $this->assertTrue($sentMessage->getOriginalMessage()->getHeaders()->has('x-custom-header'));
        $this->assertEquals('X-Custom-Header', $sentMessage->getOriginalMessage()->getHeaders()->get('x-custom-header')->getName());
        $this->assertEquals('Custom Value', $sentMessage->getOriginalMessage()->getHeaders()->get('x-custom-header')->getValue());
    }

    public function testMailableAttributesInBuild()
    {
        Container::getInstance()->instance('mailer', new class
        {
            public function render()
            {
                //
            }
        });

        $mailable = new class() extends Mailable
        {
            public function build()
            {
                $this
                    ->to('hello@quantaquirk.com')
                    ->replyTo('taylor@quantaquirk.com')
                    ->cc('cc@quantaquirk.com', 'Taylor Otwell')
                    ->bcc('bcc@quantaquirk.com', 'Taylor Otwell')
                    ->tag('test-tag')
                    ->metadata('origin', 'test-suite')
                    ->metadata('user_id', 1)
                    ->subject('test subject');
            }
        };

        $mailable->assertTo('hello@quantaquirk.com');
        $mailable->assertHasReplyTo('taylor@quantaquirk.com');
        $mailable->assertHasCc('cc@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasBcc('bcc@quantaquirk.com', 'Taylor Otwell');
        $mailable->assertHasTag('test-tag');
        $mailable->assertHasMetadata('origin', 'test-suite');
        $mailable->assertHasMetadata('user_id', 1);
        $mailable->assertHasSubject('test subject');
    }
}

class MailableHeadersStub extends Mailable
{
    public function headers()
    {
        return new Headers('custom-message-id@example.com', [
            'previous-message@example.com',
        ], [
            'X-Custom-Header' => 'Custom Value',
        ]);
    }
}

class WelcomeMailableStub extends Mailable
{
    public $framework = 'QuantaQuirk';

    protected $version = '5.3';

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->with('first_name', 'Taylor')
             ->withLastName('Otwell');
    }
}

class MailableTestUserStub
{
    public $name = 'Taylor Otwell';
    public $email = 'taylor@quantaquirk.com';
}

class MailableTestUserStub2
{
    public $name = 'QuantaQuirk Framework';
    public $email = 'contact@quantaquirk.com';
}

class MailTestAttachable implements Attachable
{
    public function __construct(protected $attachment)
    {
        //
    }

    public function toMailAttachment()
    {
        return $this->attachment;
    }
}
