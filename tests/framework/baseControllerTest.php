<?php

use \BDSCore\BaseController;

class BaseControllerTest extends \PHPUnit\Framework\TestCase
{

    private $request;

    private $reponse;

    public function setUp() {
        $this->request = new \GuzzleHttp\Psr7\ServerRequest('get', '/');
        $this->reponse = new \GuzzleHttp\Psr7\Response();
    }

    public function testInstanceOfController() {
        $controller = new BaseController($this->request, $this->reponse);
        $this->assertInstanceOf(BaseController::class, $controller);
    }

    public function testGetRequest() {
        $controller = new BaseController($this->request, $this->reponse);
        $request = $controller->getRequest();
        $this->assertEquals($this->request, $request);
    }

    public function testGetResponse() {
        $controller = new BaseController($this->request, $this->reponse);
        $response = $controller->getResponse();
        $this->assertEquals($this->reponse, $response);
    }

    public function testSetResponse() {
        $controller = new BaseController($this->request, $this->reponse);
        $response = $controller->getResponse();

        $response->getBody()->write('MyBody');
        $controller->setResponse($response);
        $this->assertEquals($this->reponse->getBody(), 'MyBody');

        $response = $response->withStatus(404);
        $this->reponse = $controller->setResponse($response);
        $this->assertEquals($this->reponse->getStatusCode(), 404);
    }

    public function testGetMethod() {
        $controller = new BaseController($this->request, $this->reponse);
        $method = $controller->getMethod();
        $this->assertEquals($method, 'GET');
    }

}