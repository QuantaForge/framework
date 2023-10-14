<?php

namespace QuantaForge\Tests\Database;

use Generator;
use QuantaForge\Database\MySqlConnection;
use QuantaForge\Database\Schema\MySqlSchemaState;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DatabaseMySqlSchemaStateTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testConnectionString(string $expectedConnectionString, array $expectedVariables, array $dbConfig): void
    {
        $connection = $this->createMock(MySqlConnection::class);
        $connection->method('getConfig')->willReturn($dbConfig);

        $schemaState = new MySqlSchemaState($connection);

        // test connectionString
        $method = new ReflectionMethod(get_class($schemaState), 'connectionString');
        $connString = $method->invoke($schemaState);

        self::assertEquals($expectedConnectionString, $connString);

        // test baseVariables
        $method = new ReflectionMethod(get_class($schemaState), 'baseVariables');
        $variables = $method->invoke($schemaState, $dbConfig);

        self::assertEquals($expectedVariables, $variables);
    }

    public static function provider(): Generator
    {
        yield 'default' => [
            ' --user="${:QUANTAFORGE_LOAD_USER}" --password="${:QUANTAFORGE_LOAD_PASSWORD}" --host="${:QUANTAFORGE_LOAD_HOST}" --port="${:QUANTAFORGE_LOAD_PORT}"', [
                'QUANTAFORGE_LOAD_SOCKET' => '',
                'QUANTAFORGE_LOAD_HOST' => '127.0.0.1',
                'QUANTAFORGE_LOAD_PORT' => '',
                'QUANTAFORGE_LOAD_USER' => 'root',
                'QUANTAFORGE_LOAD_PASSWORD' => '',
                'QUANTAFORGE_LOAD_DATABASE' => 'forge',
                'QUANTAFORGE_LOAD_SSL_CA' => '',
            ], [
                'username' => 'root',
                'host' => '127.0.0.1',
                'database' => 'forge',
            ],
        ];

        yield 'ssl_ca' => [
            ' --user="${:QUANTAFORGE_LOAD_USER}" --password="${:QUANTAFORGE_LOAD_PASSWORD}" --host="${:QUANTAFORGE_LOAD_HOST}" --port="${:QUANTAFORGE_LOAD_PORT}" --ssl-ca="${:QUANTAFORGE_LOAD_SSL_CA}"', [
                'QUANTAFORGE_LOAD_SOCKET' => '',
                'QUANTAFORGE_LOAD_HOST' => '',
                'QUANTAFORGE_LOAD_PORT' => '',
                'QUANTAFORGE_LOAD_USER' => 'root',
                'QUANTAFORGE_LOAD_PASSWORD' => '',
                'QUANTAFORGE_LOAD_DATABASE' => 'forge',
                'QUANTAFORGE_LOAD_SSL_CA' => 'ssl.ca',
            ], [
                'username' => 'root',
                'database' => 'forge',
                'options' => [
                    \PDO::MYSQL_ATTR_SSL_CA => 'ssl.ca',
                ],
            ],
        ];

        yield 'unix socket' => [
            ' --user="${:QUANTAFORGE_LOAD_USER}" --password="${:QUANTAFORGE_LOAD_PASSWORD}" --socket="${:QUANTAFORGE_LOAD_SOCKET}"', [
                'QUANTAFORGE_LOAD_SOCKET' => '/tmp/mysql.sock',
                'QUANTAFORGE_LOAD_HOST' => '',
                'QUANTAFORGE_LOAD_PORT' => '',
                'QUANTAFORGE_LOAD_USER' => 'root',
                'QUANTAFORGE_LOAD_PASSWORD' => '',
                'QUANTAFORGE_LOAD_DATABASE' => 'forge',
                'QUANTAFORGE_LOAD_SSL_CA' => '',
            ], [
                'username' => 'root',
                'database' => 'forge',
                'unix_socket' => '/tmp/mysql.sock',
            ],
        ];
    }
}
