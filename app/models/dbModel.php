<?php
namespace App\Models;
use BDSCore;

class dbModel
{
    private $database;

    public function __construct()
    {
        $this->database = new BDSCore\Database\Database();
    }


    public function bookmarkList()
    {
        $sql = 'SELECT id, url, nom FROM bookmark ORDER BY id ASC';

        $result = $this->database->fetchAll($sql);

        return $result;
    }

}