<?php

return [

    'debugLogger' => false,
    'debugBar'    => true,
    'showDebug'   => false,

    'errorLogger'    => true,
    'showExceptions' => true,

    'timezone' => 'Europe/Paris',
    'locale'   => 'fr',

    'routerCache' => false,

    'twigViews' => 'app/views',
    'twigCache' => 'cache/twig',

    'db_driver'   => $_ENV['DB_DRIVER'],
    'db_host'     => $_ENV['DB_HOST'],
    'db_name'     => $_ENV['DB_NAME'],
    'db_username' => $_ENV['DB_USERNAME'],
    'db_password' => $_ENV['DB_PASSWORD'],

];