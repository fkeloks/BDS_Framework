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

        if (\BDSCore\Config\Config::getConfig('errorLogger')) {
            $logger = new \BDSCore\Debug\Logger();
            $logger->log($e);
        }

        $template = new \BDSCore\Twig\Template();
        if (\BDSCore\Config\Config::getConfig('showExceptions')) {
            $template->render('errors/error500.twig', [
                'className' => get_class($e),
                'exception' => $e->getMessage()
            ]);
        } else {
            $template->render('errors/error500.twig', ['exception' => false]);
        }

        exit();
    } catch (Exception $ex) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        die('-[ Error 500 -]');
    }
}

set_exception_handler('catchException');

require('vendor/autoload.php');

$securityConfig = \BDSCore\Config\Config::getAllSecurityConfig();

ini_set('session.cookie_lifetime', $securityConfig['sessionLifetime']);
session_name('BDS_SESSION');
session_start();
$_SESSION['config'] = include('config/config.php');

if (isset($_GET['errorCode'])) {
    \BDSCore\Errors::returnError($_GET['errorCode']);
}

$debugClass = new \BDSCore\Debug\Debugger();
/**
 * @param $item
 * @return bool
 */
function debug($item): bool {
    global $debugClass;
    if (is_string($item)) {
        \BDSCore\Debug\debugBar::pushElement('Debug#' . substr(uniqid(), 8), $item);
    }

    return $debugClass->debug($item);
}

if ($securityConfig['checkPermissions']) {
    $security = new \BDSCore\Security\Security($securityConfig['ipBan']);
    $security->checkPermissions();
}

$config = \BDSCore\Config\Config::getAllConfig();
\BDSCore\Debug\debugBar::pushElement('DebugInFile', ($config['debugFile']) ? 'true' : 'false');
\BDSCore\Debug\debugBar::pushElement('Locale', $config['locale']);
\BDSCore\Debug\debugBar::pushElement('Timezone', $config['timezone']);

$router = new BDSCore\Router\Router();

(!isset($_SESSION['auth'])) ? $_SESSION['auth'] = false : null;
if ($securityConfig['authRequired']) {
    if ($_SESSION['auth'] !== true) {
        $router->activateLogin($securityConfig['authPage']);
        if ($config['debugBar']) {
            $timeStop = microtime(true);
            setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
        }
        if ($_SERVER['REQUEST_URI'] !== '/login') {
            header('Location: login');
            exit();
        }
    }
}

$router->run();

if ($config['debugBar']) {
    $timeStop = microtime(true);
    setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
}