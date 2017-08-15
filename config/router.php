<?php

return [

    'routerConfig' => [
        'controllersNamespace' => 'App\Controllers',
        'viewError404'         => 'errors/error404.twig',
        'viewError405'         => 'errors/error405.twig'
    ],

    'routes' => [
        'homePage'  => ['get', '/',                 'homeController@index'],
        'helloPage' => ['get', '/hello/{name:\w+}', 'helloController@index'],
        'testPage'  => ['get', '/test',             'testController@index'],
        'dbPage'    => ['get', '/db',               'dbController@index'],
    ]

];