<?php

error_reporting(E_ALL ^ E_WARNING);
$timeStart = microtime(true);

/**
 * @param $e
 */
function catchException($e) {
    try {
        require_once('vendor/autoload.php');

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        if (\BDSCore\Config::getConfig('errorLogger')) {
            $logger = new \BDSCore\Logger();
            $logger->log($e);
        }

        $template = new \BDSCore\Template();
        $template->render('errors/error500.twig');

        exit();
    } catch (Exception $ex) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        die('-[ Error 500 -]');
    }
}

set_exception_handler('catchException');

session_start();
$_SESSION['config'] = include('config/config.php');

require('vendor/autoload.php');

if (isset($_GET['errorCode'])) {
    \BDSCore\Errors::returnError($_GET['errorCode']);
}

$debugClass = new \BDSCore\Debugger();
/**
 * @param $item
 * @return bool
 */
function debug($item): bool {
    global $debugClass;
    if (is_string($item)) {
        \BDSCore\debugBar::pushElement('Debug#' . substr(uniqid(), 8), $item);
    }

    return $debugClass->debug($item);
}

$config = \BDSCore\Config::getAllConfig();
\BDSCore\debugBar::pushElement('DebugInFile', ($config['debugFile']) ? 'true' : 'false');
\BDSCore\debugBar::pushElement('Locale', $config['locale']);
\BDSCore\debugBar::pushElement('Timezone', $config['timezone']);

$router = new BDSCore\Router();
$router->run();

if ($config['debugBar']) {
    $timeStop = microtime(true);
    setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
}