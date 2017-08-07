<?php

error_reporting(E_ALL ^ E_WARNING);

$timeStart = microtime(true);

require('vendor/autoload.php');

$app = new \BDSCore\Application\App(
    [
        'globalConfig' => \BDSCore\Config\Config::getAllConfig(),
        'securityConfig' => \BDSCore\Config\Config::getAllSecurityConfig()
    ],
    [
        'debugClass' => new \BDSCore\Debug\Debugger(),
        'securityClass' => new \BDSCore\Security\Security(),
        'routerClass' => new BDSCore\Router\Router()
    ]
);

set_exception_handler([$app, 'catchException']);

function debug($item) {
    $app->debug($item);
}

$app->run($timeStart);