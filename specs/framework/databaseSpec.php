<?php

describe('Database', function () {

    it('Testing the instantiation of a Database object', function () {
        expect(function () {
            return new \BDSCore\Database\Database();
        })->toThrow(new \BDSCore\Database\DatabaseException);
        expect(function () {
            return new \BDSCore\Database\Database('databaseTest', 'd');
        })->toThrow(new \BDSCore\Database\DatabaseException);
        expect(new \BDSCore\Database\Database('databaseTest', 'sqlite'))->toBeAnInstanceOf('BDSCore\Database\Database');
        expect(function () {
            new \BDSCore\Database\Database('databaseTest', 'mysql');
        })->toThrow(new \BDSCore\Database\DatabaseException);
    });

    it('Test sending data to a sqlite database.', function () {
        $database = new \BDSCore\Database\Database('databaseTest', 'sqlite');
        expect($database->query('CREATE TABLE IF NOT EXISTS users ( id INT PRIMARY KEY, name VARCHAR(255), password VARCHAR(255) ); CREATE UNIQUE INDEX users_id_uindex ON users (id); '))->toBeAnInstanceOf('PDOStatement');
        expect($database->query('INSERT INTO users (name, password) VALUES ("Babar", "myPassword")'))->toBeAnInstanceOf('PDOStatement');
    });

    it('Testing recovery of data from a sqlite database.', function () {
        $database = new \BDSCore\Database\Database('databaseTest', 'sqlite');
        expect($database->fetch('SELECT * FROM users'))->not->toBeFalsy();
        expect($database->fetchAll('SELECT * FROM users'))->toBeA('array');
        expect($database->fetchColumn('SELECT name, password FROM users'))->not->toBeFalsy();
        expect($database->lastInsertId())->toBeA('int');
    });

    it('Deletion test of the created test database.', function () {
        if (PHP_OS == 'Linux') {
            expect(unlink('./storage/databases/randomValue.sqlite'))->toBeTruthy();
        }
    });

});