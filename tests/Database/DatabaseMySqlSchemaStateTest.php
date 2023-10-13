<?php

namespace QuantaQuirk\Tests\Database;

use Generator;
use QuantaQuirk\Database\MySqlConnection;
use QuantaQuirk\Database\Schema\MySqlSchemaState;
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
            ' --user="${:QUANTAQUIRK_LOAD_USER}" --password="${:QUANTAQUIRK_LOAD_PASSWORD}" --host="${:QUANTAQUIRK_LOAD_HOST}" --port="${:QUANTAQUIRK_LOAD_PORT}"', [
                'QUANTAQUIRK_LOAD_SOCKET' => '',
                'QUANTAQUIRK_LOAD_HOST' => '127.0.0.1',
                'QUANTAQUIRK_LOAD_PORT' => '',
                'QUANTAQUIRK_LOAD_USER' => 'root',
                'QUANTAQUIRK_LOAD_PASSWORD' => '',
                'QUANTAQUIRK_LOAD_DATABASE' => 'forge',
                'QUANTAQUIRK_LOAD_SSL_CA' => '',
            ], [
                'username' => 'root',
                'host' => '127.0.0.1',
                'database' => 'forge',
            ],
        ];

        yield 'ssl_ca' => [
            ' --user="${:QUANTAQUIRK_LOAD_USER}" --password="${:QUANTAQUIRK_LOAD_PASSWORD}" --host="${:QUANTAQUIRK_LOAD_HOST}" --port="${:QUANTAQUIRK_LOAD_PORT}" --ssl-ca="${:QUANTAQUIRK_LOAD_SSL_CA}"', [
                'QUANTAQUIRK_LOAD_SOCKET' => '',
                'QUANTAQUIRK_LOAD_HOST' => '',
                'QUANTAQUIRK_LOAD_PORT' => '',
                'QUANTAQUIRK_LOAD_USER' => 'root',
                'QUANTAQUIRK_LOAD_PASSWORD' => '',
                'QUANTAQUIRK_LOAD_DATABASE' => 'forge',
                'QUANTAQUIRK_LOAD_SSL_CA' => 'ssl.ca',
            ], [
                'username' => 'root',
                'database' => 'forge',
                'options' => [
                    \PDO::MYSQL_ATTR_SSL_CA => 'ssl.ca',
                ],
            ],
        ];

        yield 'unix socket' => [
            ' --user="${:QUANTAQUIRK_LOAD_USER}" --password="${:QUANTAQUIRK_LOAD_PASSWORD}" --socket="${:QUANTAQUIRK_LOAD_SOCKET}"', [
                'QUANTAQUIRK_LOAD_SOCKET' => '/tmp/mysql.sock',
                'QUANTAQUIRK_LOAD_HOST' => '',
                'QUANTAQUIRK_LOAD_PORT' => '',
                'QUANTAQUIRK_LOAD_USER' => 'root',
                'QUANTAQUIRK_LOAD_PASSWORD' => '',
                'QUANTAQUIRK_LOAD_DATABASE' => 'forge',
                'QUANTAQUIRK_LOAD_SSL_CA' => '',
            ], [
                'username' => 'root',
                'database' => 'forge',
                'unix_socket' => '/tmp/mysql.sock',
            ],
        ];
    }
}
