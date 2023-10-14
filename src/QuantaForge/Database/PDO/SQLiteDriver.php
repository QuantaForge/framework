<?php

namespace QuantaForge\Database\PDO;

use Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use QuantaForge\Database\PDO\Concerns\ConnectsToDatabase;

class SQLiteDriver extends AbstractSQLiteDriver
{
    use ConnectsToDatabase;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_sqlite';
    }
}
