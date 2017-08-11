<?php

namespace App\Models;

class MysqlModel {

    public function sayHello($name) {
        return 'Mysql ' . htmlentities($name);
    }

}