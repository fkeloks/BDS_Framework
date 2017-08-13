<?php

/**
 * @param $item
 * @return bool
 */
function debug($item): bool {
    \BDSCore\Debug\debugBar::pushElement('Debug#' . substr(uniqid(), 8), $item);

    return \BDSCore\Debug\Debugger::debug($item);
}

/**
 * @param $item
 * @return void
 */
function dd($item) {
    echo('<pre>');
    var_dump($item);
    echo('</pre>');
    die();
}