<?php

/**
 * Table showing the configuration of the router
 * Tableau représentant la configuration du routeur
 */
return [

    /*
     * Configuration of the router; Do not add a key to this table
     * Configuration du routeur; Ne pas ajouter de clef dans ce tableau
     */
    'routerConfig' => [

        /*
         * Namespace Controllers (Not Recommended to Change)
         * Namespace des contrôleurs (Déconseillé de changer)
         */
        'controllersNamespace' => 'App\Controllers',

        /*
         * Path of the view for errors 404 and 405
         * Chemin de la vue pour les erreurs 404 et 405
         */
        'viewError404'         => 'errors/error404.twig',
        'viewError405'         => 'errors/error405.twig'

    ],

    /*
     * Definitions of roads/url
     * Définitions des routes/url
     */
    'routes'       => [

        'homePage'  => ['get', '/', 'HomeController@index'],
        'helloPage' => ['get', '/hello/{name:\w+}', 'HelloController@index'],
        'testPage'  => ['get', '/test', 'TestController@index'],
        'dbPage'    => ['get', '/database', 'DbController@index']

        /*
         * Example: 'contactPage'  => ['get', '/contact', 'ContactController@index']
         *
         * 'contactPag': Name of the road
         * 'get': Corresponding method
         * 'contact': Path of the road (url)
         * 'ContactController@index': Name of the controller followed by the name of the method to be called
         */

    ]

];