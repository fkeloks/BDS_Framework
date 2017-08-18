<?php

namespace App\Controllers;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends \BDSCore\BaseController
{

    public function index() {
        $this->render('globals/default.twig');
    }

}