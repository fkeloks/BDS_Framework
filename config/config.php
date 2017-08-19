<?php

/**
 * Table representing the global configuration of the framework
 * Tableau représentant la configuration globale du framework
 */
return [

    /*
     * Log the result of the debug() function in storage/logs folder
     * Log le résultat de la fonction debug() dans le dossier storage/logs
     */
    'debugLogger'    => false,

    /*
     * If True, a debugBar is inserted in the view when using the render() function
     * Si True, une debugBar est insérée dans la vue lors de l'utilisation de la fonction render()
     */
    'debugBar'       => true,

    /*
     * If True, the debug() function will display a simple var_dump in a html tag "<pre>"
     * Si True, la fonction debug() affichera un simple var_dump dans une balise html "<pre>"
     */
    'showDebug'      => false,

    /*
     * If True, the captured errors and exceptions will be logged in the storage/logs folder
     * Si True, les erreurs et exceptions capturées seront log dans le dossier storage/logs
     */
    'errorLogger'    => true,

    /*
     * If True, errors and exceptions will be captured by the Whoops library
     * Si True, les erreurs et les exceptions seront capturées par la librairie Whoops
     */
    'useWhoops'      => false,

    /*
     * If False, the errors and exceptions captured will make a 500 error. (Caution, if Whoops is enabled, the errors will be captured anyway!)
     * Si False, les erreurs et exceptions capturées rendront une erreur 500. (Attention, si Whoops est activé, les erreurs seront capturées quand même!)
     */
    'showExceptions' => true,

    /*
     * Time zone and main application language
     * Fuseau horaire et langue principale de l'application
     */
    'timezone'       => 'Europe/Paris',
    'locale'         => 'fr',

    /*
     * Road Caching for More Performance
     * Mise en cache des routes pour plus de performance
     */
    'routerCache'    => false,

    /*
     * Folder containing the views (Not recommended to change)
     * Dossier contenant les vues (Déconseillé de changer)
     */
    'twigViews'      => 'app/Views',

    /*
     * Twig views for more performance. Set false to disable caching
     * Mise en cache des vues Twig pour plus de performance. Mettre False pour désactiver la mise en cache
     */
    'twigCache'      => 'cache/twig',

    /*
     * Configuring the database. By default, the parameters are contained in the .env file at the root
     * Configuration de la base de donnée. Par defaut, les paramètres sont contenus dans le fichier .env à la racine
     */
    'db_driver'      => $_ENV['DB_DRIVER'],
    'db_host'        => $_ENV['DB_HOST'],
    'db_name'        => $_ENV['DB_NAME'],
    'db_username'    => $_ENV['DB_USERNAME'],
    'db_password'    => $_ENV['DB_PASSWORD'],

];