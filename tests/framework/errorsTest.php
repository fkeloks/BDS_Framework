<?php

use \BDSCore\Errors\Errors;

class ErrorsTest extends \PHPUnit\Framework\TestCase
{

    public function testReturnError404() {
        $response = new \GuzzleHttp\Psr7\Response();
        $response = Errors::returnError($response, 404, false);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), 404);
    }

    public function testReturnError403() {
        $response = new \GuzzleHttp\Psr7\Response();
        $response = Errors::returnError($response, 403, false);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), 403);
    }

    public function testReturnErrorWithNotExistTemplate() {
        $response = new \GuzzleHttp\Psr7\Response();
        $response = Errors::returnError($response, 402, false);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), 402);
        $this->assertEquals($response->getBody(), 'Error 402<br>The customer must reformulate his request with the correct payment data.');
    }

}