<?php

use QuantaQuirk\Notifications\DatabaseNotification;
use QuantaQuirk\Notifications\DatabaseNotificationCollection;

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
assertType('QuantaQuirk\Database\Eloquent\Collection<int, QuantaQuirk\Notifications\DatabaseNotification>', $databaseNotificationsCollection);

$customNotificationsCollection = CustomNotification::all();
assertType('QuantaQuirk\Database\Eloquent\Collection<int, CustomNotification>', $customNotificationsCollection);
