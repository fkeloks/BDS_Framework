<?php

error_reporting(E_ALL ^ E_WARNING);

$timeStart = microtime(true);

require('../vendor/autoload.php');

/*
 * Loading Request and Response Objects
 * Chargement des objets Request et Response
 */
$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = new \GuzzleHttp\Psr7\Response();

\BDSCore\Config\Config::setDirectoryConfig();
\BDSCore\Application\App::loadEnv();

/*
 * Application Instantiation
 * Instanciation de l'application
 */
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

/*
 * By default, you can capture errors and raise exceptions
 * Par défaut, on capture les erreurs et les levées d'exceptions
 */
set_error_handler([$app, 'catchException']);
set_exception_handler([$app, 'catchException']);

require('../core/Functions/Functions.php');

/*
 * Launching the application
 * Lancement de l'application
 */
$app->run($request, $response, $timeStart);