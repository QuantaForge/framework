<?php

namespace QuantaForge\Tests\Integration\Database\EloquentModelLoadMissingTest;

use DB;
use QuantaForge\Database\Eloquent\Model;
use QuantaForge\Database\Schema\Blueprint;
use QuantaForge\Support\Facades\Schema;
use QuantaForge\Tests\Integration\Database\DatabaseTestCase;

class EloquentModelLoadMissingTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('post_id');
        });

        Post::create();

        Comment::create(['parent_id' => null, 'post_id' => 1]);
        Comment::create(['parent_id' => 1, 'post_id' => 1]);
    }

    public function testLoadMissing()
    {
        $post = Post::with('comments')->first();

        DB::enableQueryLog();

        $post->loadMissing('comments.parent');

        $this->assertCount(1, DB::getQueryLog());
        $this->assertTrue($post->comments[0]->relationLoaded('parent'));
    }
}

class Comment extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(self::class);
    }
}

class Post extends Model
{
    public $timestamps = false;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
