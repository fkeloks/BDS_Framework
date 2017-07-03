<?php

namespace App\Models;

class HelloModel {

    public function sayHello($name) {
        return 'Hello ' . htmlentities($name);
    }

}