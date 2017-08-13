<?php

namespace App\Controllers;

/**
 * Class HelloController
 * @package App\Controllers
 */
class HelloController extends \BDSCore\BaseController
{

    public function index($name) {
        $helloClass = new \App\Models\HelloModel();
        echo $helloClass->sayHello($name);
    }

}