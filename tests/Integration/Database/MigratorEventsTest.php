<?php

namespace QuantaQuirk\Tests\Integration\Database;

use QuantaQuirk\Database\Events\MigrationEnded;
use QuantaQuirk\Database\Events\MigrationsEnded;
use QuantaQuirk\Database\Events\MigrationsStarted;
use QuantaQuirk\Database\Events\MigrationStarted;
use QuantaQuirk\Database\Events\NoPendingMigrations;
use QuantaQuirk\Database\Migrations\Migration;
use QuantaQuirk\Support\Facades\Event;
use Orchestra\Testbench\TestCase;

class MigratorEventsTest extends TestCase
{
    protected function migrateOptions()
    {
        return [
            '--path' => realpath(__DIR__.'/stubs/'),
            '--realpath' => true,
        ];
    }

    public function testMigrationEventsAreFired()
    {
        Event::fake();

        $this->artisan('migrate', $this->migrateOptions());
        $this->artisan('migrate:rollback', $this->migrateOptions());

        Event::assertDispatched(MigrationsStarted::class, 2);
        Event::assertDispatched(MigrationsEnded::class, 2);
        Event::assertDispatched(MigrationStarted::class, 2);
        Event::assertDispatched(MigrationEnded::class, 2);
    }

    public function testMigrationEventsContainTheMigrationAndMethod()
    {
        Event::fake();

        $this->artisan('migrate', $this->migrateOptions());
        $this->artisan('migrate:rollback', $this->migrateOptions());

        Event::assertDispatched(MigrationsStarted::class, function ($event) {
            return $event->method === 'up';
        });
        Event::assertDispatched(MigrationsStarted::class, function ($event) {
            return $event->method === 'down';
        });
        Event::assertDispatched(MigrationsEnded::class, function ($event) {
            return $event->method === 'up';
        });
        Event::assertDispatched(MigrationsEnded::class, function ($event) {
            return $event->method === 'down';
        });

        Event::assertDispatched(MigrationStarted::class, function ($event) {
            return $event->method === 'up' && $event->migration instanceof Migration;
        });
        Event::assertDispatched(MigrationStarted::class, function ($event) {
            return $event->method === 'down' && $event->migration instanceof Migration;
        });
        Event::assertDispatched(MigrationEnded::class, function ($event) {
            return $event->method === 'up' && $event->migration instanceof Migration;
        });
        Event::assertDispatched(MigrationEnded::class, function ($event) {
            return $event->method === 'down' && $event->migration instanceof Migration;
        });
    }

    public function testTheNoMigrationEventIsFiredWhenNothingToMigrate()
    {
        Event::fake();

        $this->artisan('migrate');
        $this->artisan('migrate:rollback');

        Event::assertDispatched(NoPendingMigrations::class, function ($event) {
            return $event->method === 'up';
        });
        Event::assertDispatched(NoPendingMigrations::class, function ($event) {
            return $event->method === 'down';
        });
    }
}
