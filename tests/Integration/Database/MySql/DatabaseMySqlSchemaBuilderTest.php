<?php

namespace QuantaForge\Tests\Integration\Database\MySql;

use QuantaForge\Database\Schema\Blueprint;
use QuantaForge\Support\Facades\DB;
use QuantaForge\Support\Facades\Schema;

/**
 * @requires extension pdo_mysql
 * @requires OS Linux|Darwin
 */
class DatabaseMySqlSchemaBuilderTest extends MySqlTestCase
{
    public function testAddCommentToTable()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->comment('This is a comment');
        });

        $tableInfo = DB::table('information_schema.tables')
            ->where('table_schema', $this->app['config']->get('database.connections.mysql.database'))
            ->where('table_name', 'users')
            ->select('table_comment as table_comment')
            ->first();

        $this->assertEquals('This is a comment', $tableInfo->table_comment);

        Schema::drop('users');
    }
}
