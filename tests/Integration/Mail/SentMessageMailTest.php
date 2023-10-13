<?php

namespace QuantaQuirk\Tests\Integration\Mail;

use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Database\Schema\Blueprint;
use QuantaQuirk\Notifications\Events\NotificationSent;
use QuantaQuirk\Notifications\Messages\MailMessage;
use QuantaQuirk\Notifications\Notifiable;
use QuantaQuirk\Notifications\Notification;
use QuantaQuirk\Support\Facades\Event;
use QuantaQuirk\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class SentMessageMailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('sent_message_users', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    public function testDispatchesNotificationSent()
    {
        $notificationWasSent = false;

        $user = SentMessageUser::create();

        Event::listen(
            NotificationSent::class,
            function (NotificationSent $notification) use (&$notificationWasSent, $user) {
                $notificationWasSent = true;
                /**
                 * Confirm that NotificationSent can be serialized/unserialized as
                 * will happen if the listener implements ShouldQueue.
                 */
                /** @var NotificationSent $afterSerialization */
                $afterSerialization = unserialize(serialize($notification));

                $this->assertTrue($user->is($afterSerialization->notifiable));

                $this->assertEqualsCanonicalizing($notification->notification, $afterSerialization->notification);
            });

        $user->notify(new SentMessageMailNotification());

        $this->assertTrue($notificationWasSent);
    }
}

class SentMessageUser extends Model
{
    use Notifiable;

    public $timestamps = false;
}

class SentMessageMailNotification extends Notification
{
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Example notification with attachment.')
            ->attach(__DIR__.'/Fixtures/blank_document.pdf', [
                'as' => 'blank_document.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
