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
        $response = $this->getResponse();

        $response->getBody()->write($helloClass->sayHello($name));
        $this->setResponse($response);
    }

}