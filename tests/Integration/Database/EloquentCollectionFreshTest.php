<?php

namespace QuantaForge\Tests\Integration\Database;

use QuantaForge\Database\Eloquent\Collection as EloquentCollection;
use QuantaForge\Database\Schema\Blueprint;
use QuantaForge\Support\Facades\Schema;
use QuantaForge\Tests\Integration\Database\Fixtures\User;

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
            ['email' => 'quantaforge@framework.com'],
            ['email' => 'quantaforge@quantaforge.com'],
        ]);

        $collection = User::all();

        $collection->first()->delete();

        $freshCollection = $collection->fresh();

        $this->assertCount(1, $freshCollection);
        $this->assertInstanceOf(EloquentCollection::class, $freshCollection);
    }
}
