<?php

namespace App\Controllers;

/**
 * Class helloController
 * @package App\Controllers
 */
class helloController {

    public function index($name) {
        $helloClass = new \App\Models\HelloModel();
        echo $helloClass->sayHello($name);
    }

}