<?php

namespace SuperBrave\UtilityPackage\Test;

use Doctrine\DBAL\Connection;
use PDO;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Handles the client bootstrapping and database cleanup.
 */
abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * The list with table name prefixes that should be ignored during truncating.
     *
     * @var array
     */
    private $ignoredTableNamePrefixes = array(
        'migration_versions',
    );

    /**
     * Creates a client for testing the controller routes.
     */
    protected function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Empties the database.
     */
    protected function tearDown()
    {
        $entityManager = static::$container->get('doctrine')->getManager();
        $entityManager->clear();

        $this->emptyDatabase();
    }

    /**
     * Empties the database.
     */
    protected function emptyDatabase(): void
    {
        /* @var Connection $connection */
        $connection = static::$container->get('doctrine')->getConnection();
        $platform = $connection->getDatabasePlatform();
        $tableNames = $this->getTableNames($connection);

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tableNames as $tableName) {
            $connection->executeUpdate($platform->getTruncateTableSQL($tableName, false));
        }

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Returns the names of the tables that need to be truncated.
     *
     * @param Connection $connection
     *
     * @return array
     */
    private function getTableNames(Connection $connection): array
    {
        $stmt = $connection->executeQuery('SHOW TABLES;');

        return array_filter(
            $stmt->fetchAll(PDO::FETCH_COLUMN, 0),
            function ($tableName) {
                foreach ($this->ignoredTableNamePrefixes as $prefix) {
                    if (strpos($tableName, $prefix) === 0) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
