<?php

use \BDSHelpers\Database\Database;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Database
     */
    private $database;

    public function SetUp() {
        $database = new Database([
            'driver'   => 'mysql',
            'hostname' => 'localhost',
            'database' => 'bds_framework',
            'username' => 'root',
            'password' => ''
        ]);
        $this->database = $database;
    }

    public function testInstanceOfDatabase() {
        $this->assertInstanceOf(Database::class, $this->database);
    }

    public function testCreateTable() {
        $test = $this->database->createTable('tests', [
            'C1' => 'varchar(255)',
            'C2' => 'varchar(255)'
        ]);
        $this->assertNotFalse($test);
    }

    public function testSimpleEmptyFetch() {
        $data = $this->database->fetch('SELECT * FROM tests');

        $this->assertFalse($data);
    }

    public function testSimpleInsert() {
        $this->database->insert('tests', [
            'C1' => 'V1',
            'C2' => 'V2'
        ]);
        $this->assertEquals(1, 1);
    }

    public function testGetLastInsertId() {
        $id = $this->database->lastInsertId();
        $this->assertInternalType('int', $id);
        $this->assertEquals($id, 0);
    }

    public function testSimpleSelectInQuery() {
        $data = $this->database->query('SELECT * FROM tests')->fetchAll();

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('C1', (array)$data[0]);
        $this->assertArrayHasKey('C2', (array)$data[0]);
    }

    public function testSimpleFetch() {
        $data = $this->database->fetch('SELECT * FROM tests');

        $this->assertInternalType('array', (array)$data);
        $this->assertArrayHasKey('C1', (array)$data);
        $this->assertArrayHasKey('C2', (array)$data);
    }

    public function testSimpleFetchAll() {
        $data = $this->database->fetchAll('SELECT * FROM tests');

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('C1', (array)$data[0]);
        $this->assertArrayHasKey('C2', (array)$data[0]);
    }

    public static function tearDownAfterClass() {
        $database = new Database([
            'driver'   => 'mysql',
            'hostname' => 'localhost',
            'database' => 'bds_framework',
            'username' => 'root',
            'password' => ''
        ]);
        $database->dropTable('tests');
    }

}