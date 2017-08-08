<?php

error_reporting(E_ALL ^ E_WARNING);

$timeStart = microtime(true);

require('vendor/autoload.php');

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = new \GuzzleHttp\Psr7\Response();

$app = new \BDSCore\Application\App(
    [
        'globalConfig' => \BDSCore\Config\Config::getAllConfig(),
        'securityConfig' => \BDSCore\Config\Config::getAllSecurityConfig()
    ],
    [
        'debugClass' => new \BDSCore\Debug\Debugger(),
        'securityClass' => new \BDSCore\Security\Security(),
        'routerClass' => new BDSCore\Router\Router($request, $response)
    ],
    $response
);

set_error_handler([$app, 'catchException']);
set_exception_handler([$app, 'catchException']);

function debug($item) {
    $app->debug($item);
}

$app->run($request, $response, $timeStart);