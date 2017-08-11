<?php

namespace App\Controllers;

/**
 * Class mysqlController
 * @package App\Controllers
 */
class postgresqlController extends \BDSCore\BaseController
{
    /*private $app;


    public function __construct(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        parent::__construct($request, $response);

        $this->app = new \App\Models\MysqlModel();
    }*/


    public function index() {
        $this->render('postgresql.twig');
    }

}