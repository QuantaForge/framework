<?php

use QuantaForge\Notifications\DatabaseNotification;
use QuantaForge\Notifications\DatabaseNotificationCollection;

use function PHPStan\Testing\assertType;

class CustomNotification extends DatabaseNotification
{
    //
}

/**
 * @extends DatabaseNotificationCollection<int, CustomNotification>
 */
class CustomNotificationCollection extends DatabaseNotificationCollection
{
    //
}

$databaseNotificationsCollection = DatabaseNotification::all();
assertType('QuantaForge\Database\Eloquent\Collection<int, QuantaForge\Notifications\DatabaseNotification>', $databaseNotificationsCollection);

$customNotificationsCollection = CustomNotification::all();
assertType('QuantaForge\Database\Eloquent\Collection<int, CustomNotification>', $customNotificationsCollection);
