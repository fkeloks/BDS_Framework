<?php

error_reporting(E_ALL ^ E_WARNING);

$timeStart = microtime(true);

require('../vendor/autoload.php');

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = new \GuzzleHttp\Psr7\Response();

\BDSCore\Config\Config::setDirectoryConfig();

$app = new \BDSCore\Application\App(
    [
        'globalConfig'   => \BDSCore\Config\Config::getAllConfig(),
        'securityConfig' => \BDSCore\Config\Config::getAllSecurityConfig()
    ],
    [
        'securityClass' => new \BDSCore\Security\Security(),
        'routerClass'   => new BDSCore\Router\Router($request, $response)
    ],
    $response
);

set_error_handler([$app, 'catchException']);
set_exception_handler([$app, 'catchException']);

require('../core/functions/functions.php');

$app->run($request, $response, $timeStart);