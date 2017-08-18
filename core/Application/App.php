<?php

namespace BDSCore\Application;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \BDSCore\Config\Config;

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
            if (!is_dir(Config::getDirectoryRoot('/cache'))) {
                mkdir(Config::getDirectoryRoot('/cache'));
            }

            $permsCodeForCache = substr(sprintf('%o', fileperms(Config::getDirectoryRoot('/cache'))), -4);
            if ($permsCodeForCache != '0777') {
                die("The access rights to the 'cache/' folder must be granted to the framework.<br />Current access rights: {$permsCodeForCache}<br />Example: sudo chmod -R 0777 cache/");
            }

            $permsCodeForStorage = substr(sprintf('%o', fileperms(Config::getDirectoryRoot('/storage/logs'))), -4);
            if ($permsCodeForStorage != '0777') {
                die("The access rights to the 'storage/logs/' folder must be granted to the framework.<br />Current access rights: {$permsCodeForStorage}<br />Example: sudo chmod -R 0777 storage/logs");
            }

            $phpMajorVersion = PHP_MAJOR_VERSION;
            if ($phpMajorVersion < 7) {
                $phpVersion = $phpMajorVersion . '.' . PHP_MINOR_VERSION;
                die("To work properly, BDS Framework needs at least PHP version 7.0.<br />Current version of PHP: {$phpVersion}");
            }

            require_once(Config::getDirectoryRoot('/vendor/autoload.php'));

            if ($this->globalConfig['errorLogger']) {
                if (!is_dir(Config::getDirectoryRoot('/storage/logs/'))) {
                    mkdir(Config::getDirectoryRoot('/storage/logs'));
                }

                $logger = new \Monolog\Logger('BDS_Framework');
                $logDirectory = Config::getDirectoryRoot('/storage/logs/FrameworkLogs.log');
                $logger->pushHandler(new \Monolog\Handler\StreamHandler($logDirectory, \Monolog\Logger::WARNING));

                $logger->warning($e);
            }

            $args = func_get_args();
            $className = (is_int($e)) ? 'PHP_Error' : get_class($e);
            $message = (is_int($e)) ? $args[1] . ' in ' . $args[2] . ':' . $args[3] : $e->getMessage();

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
            exit();
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

    public static function loadEnv() {
        $envPath = Config::getDirectoryRoot('/.env');
        if (!file_exists($envPath)) {
            file_put_contents($envPath, '
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=BDS_Framework
DB_USERNAME=root
DB_PASSWORD=');
        }
        $dotenv = new \Dotenv\Dotenv(str_replace('.env', '', $envPath));
        $dotenv->load();
    }

    public function checkPermissions() {
        if ($this->securityConfig['checkPermissions']) {
            $this->securityClass->checkPermissions();
        }
    }

    public function pushToDebugBar() {
        \BDSCore\Debug\DebugBar::pushElement('showExceptions', ($this->globalConfig['showExceptions']) ? 'true' : 'false');
        \BDSCore\Debug\DebugBar::pushElement('Locale', $this->globalConfig['locale']);
        \BDSCore\Debug\DebugBar::pushElement('Timezone', $this->globalConfig['timezone']);
    }

    public function configureWhoops() {
        if ($this->globalConfig['useWhoops']) {
            $run = new \Whoops\Run;
            $handler = new \Whoops\Handler\PrettyPageHandler();

            $handler->addDataTable('Framework configuration', array(
                'Global Config'   => $this->globalConfig,
                'Security Config' => $this->securityConfig
            ));

            $handler->setPageTitle('BDS Framework :: Error');

            $run->pushHandler($handler);
            $run->register();
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
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $timeStart
     * @return void
     */
    public function run(RequestInterface $request, ResponseInterface $response, $timeStart) {
        $this->configureWhoops();
        $this->startSession();
        $this->pushToDebugBar();
        $response = $this->routerClass->run();

        \BDSCore\Debug\DebugBar::pushElement('RequestMethod', $request->getMethod());
        $this->insertTimeToDebugBar($timeStart);

        \Http\Response\send($response);
    }

}