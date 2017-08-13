<?php

namespace BDSCore\Application;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
    private $routerClass;

    /**
     * @var mixed
     */
    private $securityClass;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * App constructor.
     * @param array $configs
     * @param array $classes
     * @param ResponseInterface $response
     */
    public function __construct(array $configs, array $classes, ResponseInterface $response) {
        $this->globalConfig = $configs['globalConfig'];
        $this->securityConfig = $configs['securityConfig'];

        $this->securityClass = $classes['securityClass'];
        $this->routerClass = $classes['routerClass'];

        $this->response = $response;
    }

    /**
     * @param $e
     */
    public function catchException($e) {
        try {
            $permsCode = substr(sprintf('%o', fileperms(\BDSCore\Config\Config::getDirectoryRoot('/cache'))), -4);
            if ($permsCode != '0777') {
                die("The access rights to the 'cache/' folder must be granted to the framework.<br />Current access rights: {$permsCode}<br />Example: sudo chmod -R 0777 cache/");
            }

            $phpMajorVersion = PHP_MAJOR_VERSION;
            if ($phpMajorVersion < 7) {
                $phpVersion = $phpMajorVersion . '.' . PHP_MINOR_VERSION;
                die("To work properly, BDS Framework needs at least PHP version 7.0.<br />Current version of PHP: {$phpVersion}");
            }

            require_once(\BDSCore\Config\Config::getDirectoryRoot('/vendor/autoload.php'));

            if ($this->globalConfig['errorLogger']) {
                $logger = new \Monolog\Logger('BDS_Framework');
                $logDirectory = \BDSCore\Config\Config::getDirectoryRoot('/storage/logs/FrameworkLogs.log');
                $logger->pushHandler(new \Monolog\Handler\StreamHandler($logDirectory, \Monolog\Logger::WARNING));

                $logger->warning($e);
            }

            $args = func_get_args();
            $className = (is_int($e)) ? 'PHP_Error' : get_class($e);
            $message = (is_int($e)) ? $args[1] : $e->getMessage();

            $template = new \BDSCore\Template\Twig($this->response);
            if ($this->globalConfig['showExceptions']) {
                $template->render('errors/error500.twig', [
                    'className' => $className,
                    'exception' => $message
                ]);
            } else {
                $template->render('errors/error500.twig', ['exception' => false]);
            }

            $this->response = $this->response->withStatus(500);

            \Http\Response\send($this->response);
            exit();
        } catch (Exception $ex) {
            $this->response->getBody()->write('--[ Error 500 ]--');
            $this->response = $this->response->withStatus(500);

            \Http\Response\send($this->response);
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

    public function pushToDebugBar() {
        \BDSCore\Debug\debugBar::pushElement('showExceptions', ($this->globalConfig['showExceptions']) ? 'true' : 'false');
        \BDSCore\Debug\debugBar::pushElement('Locale', $this->globalConfig['locale']);
        \BDSCore\Debug\debugBar::pushElement('Timezone', $this->globalConfig['timezone']);
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
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $timeStart
     * @return void
     */
    public function run(RequestInterface $request, ResponseInterface $response, $timeStart) {
        $this->startSession();
        $this->pushToDebugBar();
        $response = $this->routerClass->run();

        \BDSCore\Debug\DebugBar::pushElement('RequestMethod', $request->getMethod());
        $this->insertTimeToDebugBar($timeStart);

        \Http\Response\send($response);
    }

}