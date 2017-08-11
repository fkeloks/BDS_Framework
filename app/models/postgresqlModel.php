<?php

namespace App\Models;

class PostgresqlModel {

    public function sayHello($name) {
        return 'Postgresql ' . htmlentities($name);
    }

}