<?php

namespace App\Controllers;

/**
 * Class mysqlController
 * @package App\Controllers
 */
class dbController extends \BDSCore\BaseController
{
    private $app;


    public function __construct(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        parent::__construct($request, $response);

        $this->app = new \App\Models\dbModel();
    }


    public function index()
    {
        $this->render('db.twig', ['bookmarks' => $this->app->bookmarkList()]);
    }

}