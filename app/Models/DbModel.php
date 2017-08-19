<?php

namespace App\Models;

/**
 * Class DbModel
 * @package App\Models
 */
class DbModel
{

    /**
     * @var \BDSHelpers\Database\Database
     */
    private $database;

    /**
     * DbModel constructor.
     */
    public function __construct() {
        $this->database = new \BDSHelpers\Database\Database();
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