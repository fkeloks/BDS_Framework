<?php

namespace BDSHelpers\Database;

use BDSCore\Config\Config;

/**
 * Class Database
 * @package BDSHelpers\Database
 */
class Database
{

    /**
     * @var
     */
    private $pdo;

    /**
     * Database constructor.
     * @param string|null $driver
     * @param string|null $databaseName
     * @throws DatabaseException
     */
    public function __construct() {
        return $this->connect();
    }

    /**
     * @param $driver
     * @param $databaseName
     * @return \PDO
     * @throws DatabaseException
     */
    public function connect() {
        $config = Config::getAllConfig();
        $params = [
            'driver'   => $config['db_driver'],
            'hostname' => $config['db_host'],
            'database' => $config['db_name'],
            'username' => $config['db_username'],
            'password' => $config['db_password']
        ];

        if ($params['driver'] == 'sqlite') {
            if ($params['database'] == null) {
                throw new DatabaseException('The name of the database must be specified');
            }

            Config::getDirectoryRoot();
            $this->pdo = new \PDO(
                'sqlite:' . Config::getDirectoryRoot("/storage/databases/{$params['database']}.sqlite")
            );
        } elseif ($params['driver'] == 'mysql') {
            try {
                $this->pdo = new \PDO("mysql:host={$params['hostname']};dbname={$params['database']};charset=UTF8", $params['username'], $params['password']);
            } catch (\Exception $e) {
                throw new DatabaseException($e->getMessage());
            }
        } elseif ($params['driver'] == 'postgresql') {
            try {
                $this->pdo = new \PDO("pgsql:dbname={$params['database']};host={$params['hostname']}", $params['username'], $params['password']);
            } catch (\DatabaseException $e) {
                throw new DatabaseException($e->getMessage());
            }
        } else {
            throw new DatabaseException('Use of an unknown driver name in the \BDSCore\Database() class.');
        }

        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->pdo;
    }

    /**
     * @param string $query
     * @param array $params
     * @param string|null $entity
     * @return \PDOStatement
     */
    public function query(string $query, array $params = [], string $entity = null) {
        if (count($params) === 0) {
            $query = $this->pdo->query($query);
        } else {
            $query = $this->pdo->prepare($query);
            $query->execute($params);
        }

        if ($entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $entity);
        }

        return $query;
    }

    /**
     * @param string $query
     * @param array $params
     * @param string|null $entity
     * @return mixed
     */
    public function fetch(string $query, array $params = [], string $entity = null) {
        return $this->query($query, $params, $entity)->fetch();
    }

    /**
     * @param string $query
     * @param array $params
     * @param string|null $entity
     * @return array
     */
    public function fetchAll(string $query, array $params = [], string $entity = null): array {
        return $this->query($query, $params, $entity)->fetchAll();
    }

    /**
     * @param string $query
     * @param array $params
     * @param string|null $entity
     */
    public function fetchColumn(string $query, array $params = [], string $entity = null) {
        return $this->query($query, $params, $entity)->fetchColumn();
    }

    /**
     * @return int
     */
    public function lastInsertId(): int {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param string|null $tableName
     * @param $collumns
     * @param $values
     * @return \PDOStatement
     * @throws DatabaseException
     */
    public function insert(string $tableName = null, $collumns, $values) {
        if ($tableName != null && $collumns != null && $values != null) {
            if (is_string($collumns) && is_string($values)) {
                return $this->query("INSERT INTO {$tableName} ({$collumns}) VALUES (?)", [$values]);
            } elseif (is_array($collumns) && is_array($values)) {
                $collumnsStr = implode(', ', $collumns);
                $valuesStr = '"' . implode('", "', $values) . '"';

                return $this->query("INSERT INTO {$tableName} ({$collumnsStr}) VALUES ({$valuesStr})");
            } else {
                throw new DatabaseException('The type of parameters returned to the insert () function are invalid.');
            }
        } else {
            throw new DatabaseException('The name of the table, the name of a column (s), and the values to be inserted are mandatory parameters to execute the query.');
        }
    }

    /**
     * @param string|null $tableName
     * @param null $collums
     * @return array
     * @throws DatabaseException
     */
    public function select(string $tableName = null, $collums = null): array {
        if ($tableName != null && $collums != null) {
            if (is_string($collums)) {
                ($collums == 'all') ? $collums = '*' : null;

                return $this->query("SELECT {$collums} FROM {$tableName}")->fetchAll();
            } elseif (is_array($collums)) {
                $collumsStr = implode(', ', $collums);

                return $this->query("SELECT {$collumsStr} FROM {$tableName}")->fetchAll();
            }
        } else {
            throw new DatabaseException('The name of the table and the name of a column (s) to be inserted are mandatory parameters to execute the query.');
        }
    }

}