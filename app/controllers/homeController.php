<?php

namespace App\Controllers;

class homeController extends \BDSCore\BaseController
{

    public function index() {
        $this->render('globals/default.twig');
    }

}