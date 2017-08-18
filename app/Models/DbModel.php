<?php

namespace App\Models;

/**
 * Class DbModel
 * @package App\Models
 */
class DbModel
{

    /**
     * @var \BDSCore\Database\Database
     */
    private $database;

    /**
     * DbModel constructor.
     */
    public function __construct() {
        $this->database = new \BDSCore\Database\Database();
    }

    /**
     * @return array
     */
    public function bookmarkList() {
        $sql = 'SELECT id, url, nom FROM bookmark ORDER BY id ASC';
        $result = $this->database->fetchAll($sql);

        return $result;
    }

}