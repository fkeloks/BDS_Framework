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
     * @var array|mixed Global configuration
     */
    private $globalConfig = [];

    /**
     * @var array|mixed Security configuration
     */
    private $securityConfig = [];

    /**
     * @var mixed Instance of router class
     */
    private $routerClass;

    /**
     * @var mixed Instance of security class
     */
    private $securityClass;

    /**
     * @var ResponseInterface Response
     */
    private $response;

    /**
     * Constructor of the class
     * Constructeur de la classe
     *
     * @param array $configs Framework configuration
     * @param array $classes Framework classes
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
     * Function called in the event of an error or exception thrown.
     * Fonction appelée en cas d'erreur ou de levée d'exception.
     *
     * @param $e Exception or error
     *
     * @return void
     */
    public function catchException($e) {
        try {
            if (!is_dir(Config::getDirectoryRoot('/cache'))) {
                mkdir(Config::getDirectoryRoot('/cache'));
            }

            $isOkCache = is_writable(Config::getDirectoryRoot('/cache'));
            if (!$isOkCache) {
                die("The access rights to the 'cache/' folder must be granted to the framework.<br />Example: sudo chmod -R 0777 cache/");
            }

            $isOkLogs = is_writable(Config::getDirectoryRoot('/storage/logs'));
            if (!$isOkLogs) {
                die("The access rights to the 'storage/logs/' folder must be granted to the framework.<br />Example: sudo chmod -R 0777 storage/logs");
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

            self::send($this->response);
            exit();
        } catch (Exception $ex) {
            $this->response->getBody()->write('--[ Error 500 ]--');
            $this->response = $this->response->withStatus(500);

            self::send($this->response);
            exit();
        }
    }

    /**
     * Starting the PHP session
     * Lancement de la session PHP
     *
     * @return int
     */
    public function startSession(): int {
        ini_set('session.cookie_lifetime', $this->securityConfig['sessionLifetime']);
        session_name('BDS_SESSION');
        session_start();

        return session_status();
    }

    /**
     * Loading the .env file into the $ _ENV variable
     * Chargement du fichier .env dans la variable $_ENV
     *
     * @return void
     */
    public static function loadEnv() {
        $envPath = Config::getDirectoryRoot('/.env');
        if (!file_exists($envPath)) {
            file_put_contents($envPath, '
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=bds_framework
DB_USERNAME=root
DB_PASSWORD=');
        }
        if (!file_exists($envPath)) {
            die('The .env file is not present at the root of the framework.<br>Try renaming the .env.example file to .env and reload the page.');
        }
        $dotenv = new \Dotenv\Dotenv(str_replace('.env', '', $envPath));
        $dotenv->load();
    }

    /**
     * Checks the permissions configured in the security configuration. (Checks the IP in particular)
     * Vérifie les permissions configurée dans la configuration de la sécurité. (Vérifie les IP notamment)
     *
     * @return void
     */
    public function checkPermissions() {
        if ($this->securityConfig['checkPermissions']) {
            $this->securityClass->checkPermissions();
        }
    }

    /**
     * Inserting some elements of the configuration file for quick access via the debugBar
     * Insertion de quelques élements du fichier de configuration pour un accès rapide via la debugBar
     *
     * @return void
     */
    public function pushToDebugBar() {
        \BDSCore\Debug\DebugBar::pushElement('showExceptions', ($this->globalConfig['showExceptions']) ? 'true' : 'false');
        \BDSCore\Debug\DebugBar::pushElement('Locale', $this->globalConfig['locale']);
        \BDSCore\Debug\DebugBar::pushElement('Timezone', $this->globalConfig['timezone']);
    }

    /**
     * Configuring the Whoops library if it is enabled in the global configuration file
     * Configuration de la librairie Whoops si celle-ci est activée dans le fichier de configuration global
     *
     * @return void
     */
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
     * Inserts the execution time of the PHP script into the debugBar
     * Insère le temps d'exécution du script PHP dans la debugBar
     *
     * @param $timeStart First time capture
     */
    public function insertTimeToDebugBar($timeStart) {
        if ($this->globalConfig['debugBar']) {
            $timeStop = microtime(true);
            setcookie('BDS_loadingTime', '~' . round(($timeStop - $timeStart), 3) * 1000 . 'ms', time() + 15);
        }
    }

    /**
     * Send a response
     * Envoi uen réponse
     *
     * @param ResponseInterface $response
     *
     * @return void
     */
    public static function send(ResponseInterface $response) {
        $http_line = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        $version = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        $headerStr = "HTTP/{$version} {$status} {$reason}";
        header($headerStr, true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $response->getBody();

        ($stream->isSeekable()) ? $stream->rewind() : null;

        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }

    /**
     * Launches the application with the functions defined above
     * Lance l'application avec les fonctions définies ci-dessus
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $timeStart First time capture
     *
     * @return void
     */
    public function run(RequestInterface $request, ResponseInterface $response, $timeStart) {
        $this->configureWhoops();
        $this->startSession();
        $this->pushToDebugBar();
        $response = $this->routerClass->run();

        \BDSCore\Debug\DebugBar::pushElement('RequestMethod', $request->getMethod());
        $this->insertTimeToDebugBar($timeStart);

        self::send($response);
    }

}