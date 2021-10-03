<?php

namespace App\Tests\Tools;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

class DBPurger
{
    const TEST_CONNECTION_ROWS_LIMIT = 100;
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
        $entityManager->getConnection()->getDatabase();
    }

    public function purge()
    {
        if ($this->isProductionConnection($this->connection)) {
            throw new \RuntimeException('Trying to purge production database');
        }

        $this->truncateTables($this->connection);
    }

    private function isProductionConnection(Connection $connection) : bool
    {
        $dbName = $connection->getDatabase();
        $stmnt = $connection->prepare("SELECT SUM(TABLE_ROWS) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :db;");
        $stmnt->bindParam(':db', $dbName);
        $stmnt->executeStatement();

        return $stmnt->fetchColumn() > self::TEST_CONNECTION_ROWS_LIMIT;
    }

    private function truncateTables(Connection $connection) {
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $tables = $connection->fetchAllAssociative("SELECT `table_name` FROM information_schema.tables
                        WHERE table_schema = '{$connection->getDatabase()}';");

        foreach ($tables as $table) {
            $connection->executeQuery(sprintf('truncate table %s', $table['table_name']));
        }

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }

}