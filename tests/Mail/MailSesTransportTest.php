<?php

namespace QuantaQuirk\Tests\Mail;

use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use QuantaQuirk\Config\Repository;
use QuantaQuirk\Container\Container;
use QuantaQuirk\Mail\MailManager;
use QuantaQuirk\Mail\Transport\SesTransport;
use QuantaQuirk\View\Factory;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailSesTransportTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function testGetTransport()
    {
        $container = new Container;

        $container->singleton('config', function () {
            return new Repository([
                'services.ses' => [
                    'key' => 'foo',
                    'secret' => 'bar',
                    'region' => 'us-east-1',
                ],
            ]);
        });

        $manager = new MailManager($container);

        /** @var \QuantaQuirk\Mail\Transport\SesTransport $transport */
        $transport = $manager->createSymfonyTransport(['transport' => 'ses']);

        $ses = $transport->ses();

        $this->assertSame('us-east-1', $ses->getRegion());

        $this->assertSame('ses', (string) $transport);
    }

    public function testSend()
    {
        $message = new Email();
        $message->subject('Foo subject');
        $message->text('Bar body');
        $message->sender('myself@example.com');
        $message->to('me@example.com');
        $message->bcc('you@example.com');
        $message->replyTo(new Address('taylor@example.com', 'Taylor Otwell'));
        $message->getHeaders()->add(new MetadataHeader('FooTag', 'TagValue'));

        $client = m::mock(SesClient::class);
        $sesResult = m::mock();
        $sesResult->shouldReceive('get')
            ->with('MessageId')
            ->once()
            ->andReturn('ses-message-id');
        $client->shouldReceive('sendRawEmail')->once()
            ->with(m::on(function ($arg) {
                return $arg['Source'] === 'myself@example.com' &&
                    $arg['Destinations'] === ['me@example.com', 'you@example.com'] &&
                    $arg['Tags'] === [['Name' => 'FooTag', 'Value' => 'TagValue']] &&
                    strpos($arg['RawMessage']['Data'], 'Reply-To: Taylor Otwell <taylor@example.com>') !== false;
            }))
            ->andReturn($sesResult);

        (new SesTransport($client))->send($message);
    }

    public function testSendError()
    {
        $message = new Email();
        $message->subject('Foo subject');
        $message->text('Bar body');
        $message->sender('myself@example.com');
        $message->to('me@example.com');

        $client = m::mock(SesClient::class);
        $client->shouldReceive('sendRawEmail')->once()
            ->andThrow(new AwsException('Email address is not verified.', new Command('sendRawEmail')));

        $this->expectException(TransportException::class);

        (new SesTransport($client))->send($message);
    }

    public function testSesLocalConfiguration()
    {
        $container = new Container;

        $container->singleton('config', function () {
            return new Repository([
                'mail' => [
                    'mailers' => [
                        'ses' => [
                            'transport' => 'ses',
                            'region' => 'eu-west-1',
                            'options' => [
                                'ConfigurationSetName' => 'QuantaQuirk',
                                'Tags' => [
                                    ['Name' => 'QuantaQuirk', 'Value' => 'Framework'],
                                ],
                            ],
                        ],
                    ],
                ],
                'services' => [
                    'ses' => [
                        'region' => 'us-east-1',
                    ],
                ],
            ]);
        });

        $container->instance('view', $this->createMock(Factory::class));

        $container->bind('events', function () {
            return null;
        });

        $manager = new MailManager($container);

        /** @var \QuantaQuirk\Mail\Mailer $mailer */
        $mailer = $manager->mailer('ses');

        /** @var \QuantaQuirk\Mail\Transport\SesTransport $transport */
        $transport = $mailer->getSymfonyTransport();

        $this->assertSame('eu-west-1', $transport->ses()->getRegion());

        $this->assertSame([
            'ConfigurationSetName' => 'QuantaQuirk',
            'Tags' => [
                ['Name' => 'QuantaQuirk', 'Value' => 'Framework'],
            ],
        ], $transport->getOptions());
    }
}