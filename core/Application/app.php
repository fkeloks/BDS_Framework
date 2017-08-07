<?php

namespace BDSCore\Application;

/**
 * Class App
 * @package BDSCore\Application
 */
class App
{

    /**
     * @var array
     */
    private $globalConfig = [];

    /**
     * @var array
     */
    private $securityConfig = [];

    /**
     * @var mixed
     */
    private $debugClass;

    /**
     * @var mixed
     */
    private $routerClass;

    /**
     * @var mixed
     */
    private $securityClass;

    /**
     * App constructor.
     * @param array $configs
     * @param array $classes
     */
    public function __construct(array $configs, array $classes) {
        $this->globalConfig = $configs['globalConfig'];
        $this->securityConfig = $configs['securityConfig'];

        $this->debugClass = $classes['debugClass'];
        $this->securityClass = $classes['securityClass'];
        $this->routerClass = $classes['routerClass'];
    }

    /**
     * @param $e
     */
    public function catchException($e) {
        try {
            $permsCode = substr(sprintf('%o', fileperms('cache/')), -4);
            if ($permsCode != '0777') {
                die("The access rights to the 'cache/' folder must be granted to the framework.<br />Current access rights: {$permsCode}<br />Example: sudo chmod -R 0777 cache/");
            }

            $phpMajorVersion = PHP_MAJOR_VERSION;
            if ($phpMajorVersion < 7) {
                $phpVersion = $phpMajorVersion . '.' . PHP_MINOR_VERSION;
                die("To work properly, BDS Framework needs at least PHP version 7.0.<br />Current version of PHP: {$phpVersion}");
            }

            require_once('vendor/autoload.php');

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

            if ($this->globalConfig['errorLogger']) {
                $logger = new \Monolog\Logger('BDS_Framework');
                $logger->pushHandler(new \Monolog\Handler\StreamHandler('storage/logs/frameworkLogs.log', \Monolog\Logger::WARNING));

                $logger->warning($e);
            }

            $template = new \BDSCore\Twig\Template();
            if ($this->globalConfig['showExceptions']) {
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

    /**
     * @return int
     */
    public function startSession(): int {
        ini_set('session.cookie_lifetime', $this->securityConfig['sessionLifetime']);
        session_name('BDS_SESSION');
        session_start();

        return session_status();
    }

    public function checkPermissions() {
        if ($this->securityConfig['checkPermissions']) {
            $this->securityClass->checkPermissions();
        }
    }

    /**
     * @param $item
     * @return bool
     */
    public function debug($item): bool {
        \BDSCore\Debug\debugBar::pushElement('Debug#' . substr(uniqid(), 8), $item);

        return $this->debugClass->debug($item);
    }

    public function pushToDebugBar() {
        \BDSCore\Debug\debugBar::pushElement('showExceptions', ($this->globalConfig['showExceptions']) ? 'true' : 'false');
        \BDSCore\Debug\debugBar::pushElement('Locale', $this->globalConfig['locale']);
        \BDSCore\Debug\debugBar::pushElement('Timezone', $this->globalConfig['timezone']);
    }

    /**
     * @param $timeStart
     */
    public function checkAuth($timeStart) {
        (!isset($_SESSION['auth'])) ? $_SESSION['auth'] = false : null;
        if ($this->securityConfig['authRequired']) {
            if ($_SESSION['auth'] !== true) {
                $router->activateLogin($this->securityConfig['authPage']);
                if ($this->globalConfig['debugBar']) {
                    $timeStop = microtime(true);
                    setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
                }
                if ($_SERVER['REQUEST_URI'] !== '/login') {
                    header('Location: login');
                    exit();
                }
            }
        }
    }

    /**
     * @param $timeStart
     */
    public function insertTimeToDebugBar($timeStart) {
        if ($this->globalConfig['debugBar']) {
            $timeStop = microtime(true);
            setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
        }
    }

    /**
     * @param $timeStart
     */
    public function run($timeStart) {
        (isset($_GET['errorCode'])) ? \BDSCore\Errors\Errors::returnError($_GET['errorCode']) : null;
        $this->startSession();
        $this->checkPermissions();
        $this->checkAuth($timeStart);
        $this->pushToDebugBar();
        $this->routerClass->run();
        $this->insertTimeToDebugBar($timeStart);
    }

}