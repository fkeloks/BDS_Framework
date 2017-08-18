<?php

namespace App\Models;

/**
 * Class HelloModel
 * @package App\Models
 */
class HelloModel {

    /**
     * @param $name
     * @return string
     */
    public function sayHello($name) {
        return 'Hello ' . htmlentities($name);
    }

}