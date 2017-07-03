<?php

return [

    'routerConfig' => [
        'controllersNamespace' => 'App\Controllers',
        'viewError404'         => 'errors/error404.twig'
    ],

    'routes' => [
        'homePage'  => ['get', '/', 'homeController@index'],
        'helloPage' => ['get', '/hello/(\w+)', 'helloController@index'],
        'testPage'  => ['get', '/test', 'testController@index'],
    ]

];