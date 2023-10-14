<?php

namespace QuantaForge\Database\PDO;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use QuantaForge\Database\PDO\Concerns\ConnectsToDatabase;

class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_pgsql';
    }
}
