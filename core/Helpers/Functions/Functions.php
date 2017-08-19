<?php

/**
 * File containing the global framework functions
 * Fichier contenant les fonctions globales au framework
 */


/**
 * Inserts a variable into the debugBar and log if enabled in the global configuration
 * Insère une variable dans la debugBar et la log si activé dans la configuration globale
 *
 * @param $item Element to debug
 *
 * @return bool
 */
function debug($item): bool {
    \BDSCore\Debug\DebugBar::pushElement('Debug#' . substr(uniqid(), 8), $item);

    return \BDSCore\Debug\Debugger::debug($item);
}

/**
 * Displays a variable and then cuts the script
 * Affiche une variable puis coupe le script
 *
 * @param $item Element to debug
 *
 * @return void
 */
function dd($item) {
    echo('<pre>');
    var_dump($item);
    echo('</pre>');
    die();
}