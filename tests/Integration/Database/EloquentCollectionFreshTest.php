<?php

namespace QuantaQuirk\Tests\Integration\Database;

use QuantaQuirk\Database\Eloquent\Collection as EloquentCollection;
use QuantaQuirk\Database\Schema\Blueprint;
use QuantaQuirk\Support\Facades\Schema;
use QuantaQuirk\Tests\Integration\Database\Fixtures\User;

class EloquentCollectionFreshTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
        });
    }

    public function testEloquentCollectionFresh()
    {
        User::insert([
            ['email' => 'quantaquirk@framework.com'],
            ['email' => 'quantaquirk@quantaquirk.com'],
        ]);

        $collection = User::all();

        $collection->first()->delete();

        $freshCollection = $collection->fresh();

        $this->assertCount(1, $freshCollection);
        $this->assertInstanceOf(EloquentCollection::class, $freshCollection);
    }
}
