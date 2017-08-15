<?php

namespace App\Controllers;

class DbController extends \BDSCore\BaseController
{

    /**
     * @var \App\Models\dbModel
     */
    private $app;

    /**
     * DbController constructor.
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response) {
        parent::__construct($request, $response);

        $this->app = new \App\Models\DbModel();
    }

    /**
     * @return void
     */
    public function index() {
        $this->render('db.twig', [
            'bookmarks' => $this->app->bookmarkList()
        ]);
    }

}