<?php

namespace BDSCore\Database;

/**
 * Class Database
 * @package BDSCore\Database
 */
class Database
{

    /**
     * @var
     */
    private $pdo;

    /**
     * Database constructor.
     * @param string $databaseName
     * @param string|null $driver
     */
    public function __construct(string $databaseName = null, string $driver = null) {
        if($databaseName == null) {
            throw new DatabaseException('The name of the database must be specified');
        }
        if (!is_string($driver)) {
            $driver = \BDSCore\Config::getConfig('db_driver');
        }
        return $this->connect($driver, $databaseName);
    }

    /**
     * @param string $driver
     * @param string $databaseName
     * @return \PDOStatement
     * @throws DatabaseException
     */
    public function connect(string $driver, string $databaseName) {
        if ($driver == 'sqlite') {
            $this->pdo = new \PDO("sqlite:./storage/databases/{$databaseName}.sqlite");
        } elseif ($driver == 'mysql') {
            $params = [
                'host'     => \BDSCore\Config::getConfig('db_host'),
                'name'     => \BDSCore\Config::getConfig('db_name'),
                'username' => \BDSCore\Config::getConfig('db_username'),
                'password' => \BDSCore\Config::getConfig('db_password')
            ];
            try {
                $this->pdo = new \PDO("mysql:host={$params['host']};dbname={$params['name']};charset=UTF8", $params['username'], $params['password']);
            } catch (\Exception $e) {
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

}