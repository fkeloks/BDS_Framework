<?php

namespace BDSHelpers\Database;

use BDSCore\Config\Config;

/**
 * Database Class: Database Management
 * Base de données Classe: Gestion de bases de données
 *
 * @package BDSHelpers\Database
 */
class Database
{

    /**
     * @var \PDO Instance of PDO
     */
    private $pdo;

    /**
     * @var MysqlConnector|PgsqlConnector Actual connector (driver)
     */
    private $connector;

    /**
     * @var array Parameters
     */
    private $params = [];

    /**
     * Constructor of the class
     * Constructeur de la class
     *
     * @param array|null $params Settings for connection to the database
     */
    public function __construct(array $params = null) {
        if (is_null($params)) {
            $config = Config::getAllConfig();
            $this->params = [
                'driver'   => $config['db_driver'],
                'hostname' => $config['db_host'],
                'database' => $config['db_name'],
                'username' => $config['db_username'],
                'password' => $config['db_password']
            ];
        } else {
            $this->params = $params;
        }

        $this->connect();
    }

    /**
     * Login to the database
     * Connexion à la base de donnée
     *
     * @throws DatabaseException
     */
    private function connect() {
        $params = $this->params;

        switch ($params['driver']) {
            case 'mysql':
                $this->pdo = MysqlConnector::getPDO($params);
                $this->connector = MysqlConnector::class;
                break;
            case 'pgsql':
                $this->pdo = PgsqlConnector::getPDO($params);
                $this->connector = PgsqlConnector::class;
                break;
            default:
                throw new DatabaseException('Use of an unknown driver name in the Database() class.');
        }

        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Sends a specified request to the currently connected database
     * Envoi une requête spécifiée sur la base de donnée actuellement connectée
     *
     * @param string $query Request
     * @param array $params Arguments for the prepared query
     *
     * @return \PDOStatement|string
     */
    public function query(string $query, array $params = []) {
        if (count($params) === 0) {
            $query = $this->pdo->query($query);
        } else {
            $query = $this->pdo->prepare($query);
            $query->execute($params);
        }

        return $query;
    }

    /**
     * Search a data in the database
     * Recherche une donnée dans la base
     *
     * @param string $query Request
     * @param array $params Arguments for the prepared query
     *
     * @return mixed
     */
    public function fetch(string $query, array $params = []) {
        return $this->query($query, $params)->fetch();
    }

    /**
     * Search all data matching the query in the database
     * Recherche toute les données correspondante à la requête dans la base
     *
     * @param string $query Request
     * @param array $params Arguments for the prepared query
     *
     * @return array Results
     */
    public function fetchAll(string $query, array $params = []): array {
        return $this->query($query, $params)->fetchAll();
    }

    /**
     * Retrieves the last id inserted into the database
     * Récupère le dernier id inseré dans la base
     *
     * @return int
     */
    public function lastInsertId(): int {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $tableName
     * @param array $insert
     * @return \PDOStatement|string
     * @throws DatabaseException
     */
    public function insert(string $tableName, array $insert) {
        if (!empty($tableName) || !empty($insert)) {
            $collumns = implode(', ', array_keys($insert));
            $values = array_values($insert);

            $bind = str_repeat('?, ', count(array_keys($insert)));
            $bind = substr($bind, 0, -2);

            $sql = sprintf($this->connector::getQuery('insert'), $tableName, $collumns, $bind);

            return $this->query($sql, $values);
        } else {
            throw new DatabaseException('The name of the table, and the values to be inserted are mandatory parameters to execute the query.');
        }
    }

    /**
     * Create a table
     * Créer une table
     *
     * @param string $tableName Name of table
     * @param array $collumns Collumns with types
     *
     * @return \PDOStatement|string
     */
    public function createTable(string $tableName, array $collumns) {
        $fields = '';
        foreach ($collumns as $collumn => $type) {
            $fields .= ", {$collumn} {$type}";
        }
        $sql = sprintf($this->connector::getQuery('createTable'), $tableName, substr($fields, 2));

        return $this->query($sql);
    }

    /**
     * Drop a table
     * Supprime une table
     *
     * @param string $tableName Table name
     *
     * @return \PDOStatement|string
     */
    public function dropTable(string $tableName) {
        return $this->query(sprintf($this->connector::getQuery('dropTable'), $tableName));
    }

    /**
     * Start PDO transaction
     * Commence la transaction PDO
     *
     * @return bool
     */
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    /**
     * Rolls back
     * Effectue un retour en arrière
     *
     * @return bool
     */
    public function rollback(): bool {
        return $this->pdo->rollBack();
    }

}