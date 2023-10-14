<?php

namespace QuantaForge\Tests\Notifications;

use QuantaForge\Notifications\Channels\DatabaseChannel;
use QuantaForge\Notifications\Messages\DatabaseMessage;
use QuantaForge\Notifications\Notification;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class NotificationDatabaseChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testDatabaseChannelCreatesDatabaseRecordWithProperData()
    {
        $notification = new NotificationDatabaseChannelTestNotification;
        $notification->id = 1;
        $notifiable = m::mock();

        $notifiable->shouldReceive('routeNotificationFor->create')->with([
            'id' => 1,
            'type' => get_class($notification),
            'data' => ['invoice_id' => 1],
            'read_at' => null,
        ]);

        $channel = new DatabaseChannel;
        $channel->send($notifiable, $notification);
    }

    public function testCorrectPayloadIsSentToDatabase()
    {
        $notification = new NotificationDatabaseChannelTestNotification;
        $notification->id = 1;
        $notifiable = m::mock();

        $notifiable->shouldReceive('routeNotificationFor->create')->with([
            'id' => 1,
            'type' => get_class($notification),
            'data' => ['invoice_id' => 1],
            'read_at' => null,
            'something' => 'else',
        ]);

        $channel = new ExtendedDatabaseChannel;
        $channel->send($notifiable, $notification);
    }

    public function testCustomizeTypeIsSentToDatabase()
    {
        $notification = new NotificationDatabaseChannelCustomizeTypeTestNotification;
        $notification->id = 1;
        $notifiable = m::mock();

        $notifiable->shouldReceive('routeNotificationFor->create')->with([
            'id' => 1,
            'type' => 'MONTHLY',
            'data' => ['invoice_id' => 1],
            'read_at' => null,
            'something' => 'else',
        ]);

        $channel = new ExtendedDatabaseChannel;
        $channel->send($notifiable, $notification);
    }
}

class NotificationDatabaseChannelTestNotification extends Notification
{
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage(['invoice_id' => 1]);
    }
}

class NotificationDatabaseChannelCustomizeTypeTestNotification extends Notification
{
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage(['invoice_id' => 1]);
    }

    public function databaseType()
    {
        return 'MONTHLY';
    }
}

class ExtendedDatabaseChannel extends DatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification)
    {
        return array_merge(parent::buildPayload($notifiable, $notification), [
            'something' => 'else',
        ]);
    }
}
