<?php

namespace BDSHelpers\Database;

/**
 * Connector for PostgreSQL bases
 * Connecteur pour les bases PostgreSQL
 *
 * @package BDSHelpers\Database
 */
class PgsqlConnector
{

    /**
     * Return an instance of PDO
     * Retourne une instance de PDO
     *
     * @param array $params Parameters
     *
     * @return \PDO
     * @throws DatabaseException
     */
    public static function getPDO(array $params) {
        try {
            $pdo = new \PDO("pgsql:host={$params['hostname']};dbname={$params['database']}", $params['username'], $params['password']);
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage());
        }

        return $pdo;
    }

    /**
     * Returns the query to be executed for an action defined with this connector
     * Retourne la requête à exécuter pour une action définie avec ce connecteur
     *
     * @param string $queryName Query Nname
     *
     * @return string
     * @throws DatabaseException
     */
    public static function getQuery(string $queryName): string {
        switch ($queryName) {
            case 'insert':
                return 'INSERT INTO %s (%s) VALUES (%s)';
                break;
            case 'createTable':
                return 'CREATE TABLE %s (%s);';
                break;
            case 'dropTable':
                return 'DROP TABLE %s';
                break;
            default:
                throw new DatabaseException('Invalid queryName in getQuery() function');
        }
    }

}