<?php

namespace App\Controllers;

class testController extends \BDSCore\BaseController
{

    public function index() {
        $this->render('test.twig');
    }

}