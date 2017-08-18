<?php

namespace App\Controllers;

/**
 * Class TestController
 * @package App\Controllers
 */
class TestController extends \BDSCore\BaseController
{

    public function index() {
        $this->render('test.twig');
    }

}