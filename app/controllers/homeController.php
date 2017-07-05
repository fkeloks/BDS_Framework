<?php

namespace App\Controllers;

/**
 * Class homeController
 * @package App\Controllers
 */
class homeController extends \BDSCore\BaseController
{

    public function index() {
        $this->render('globals/default.twig');
    }

}