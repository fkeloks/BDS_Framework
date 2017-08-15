<?php

return [

    'routerConfig' => [
        'controllersNamespace' => 'App\Controllers',
        'viewError404'         => 'errors/error404.twig',
        'viewError405'         => 'errors/error405.twig'
    ],

    'routes' => [
        'homePage'  => ['get', '/', 'HomeController@index'],
        'helloPage' => ['get', '/hello/{name:\w+}', 'HelloController@index'],
        'testPage'  => ['get', '/test', 'TestController@index'],
        'dbPage'    => ['get', '/database', 'DbController@index'],
    ]

];