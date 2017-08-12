<?php

namespace BDSCore\Router;

use \BDSCore\Config\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 * @package BDSCore\Router
 */
class Router
{

    /**
     * @var \Bramus\Router\Router
     */
    private $routerClass;
    /**
     * @var mixed
     */
    private $configRouter;
    /**
     * @var Template
     */
    private $templateClass;
    /**
     * @var array
     */
    private static $paths = [];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Router constructor.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response) {
        $this->request = $request;
        $this->response = $response;

        $this->configRouter = \BDSCore\Config\Config::getAllRouterConfig();
        $this->templateClass = new \BDSCore\Template\Twig($response);
    }

    /**
     * @param string $routeName
     * @param string $path
     */
    private function setPath(string $routeName, string $path) {
        $this::$paths[$routeName] = $path;
    }

    /**
     * @param string|null $routeName
     * @param array $params
     * @return string
     */
    public static function getPath(string $routeName = null, array $params = []): string {
        if ($routeName == null) {
            return '';
        }
        if (isset(self::$paths[$routeName])) {
            if (!empty($params)) {
                $path = self::$paths[$routeName];
                foreach ($params as $p) {
                    $path = preg_replace('/\((.*)\)/U', $p, $path, 1);
                }

                return $path;
            } else {
                return self::$paths[$routeName];
            }
        } else {
            return '';
        }
    }

    /**
     * @param $method
     * @param array $args
     * @return array
     */
    private function getArgsByName($method, array $args = array()): array {
        $reflection = new \ReflectionMethod($method[0], $method[1]);

        $pass = array();
        foreach ($reflection->getParameters() as $param) {
            if (isset($args[$param->getName()])) {
                $pass[] = $args[$param->getName()];
            } else {
                $pass[] = null;
            }
        }

        return $pass;
    }

    /**
     * @return string
     */
    private function getBasePath(): string {
        $basePath = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);
        $basePath = str_replace($basePath, '', $uri);

        return $basePath;
    }

    /**
     * @return void
     */
    private function controlPerms() {
        $prohibitedDirs = [
            'app',
            'bin',
            'cache',
            'config',
            'core',
            'storage',
            'tests',
            'vendor',
        ];
        $baseDir = substr($this->getBasePath(), 1);
        $actualDir = explode('/', $baseDir);
        if (in_array($actualDir[0], $prohibitedDirs)) {
            \BDSCore\Errors\Errors::returnError($this->response, 403);
        }
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    private function configureDispatcher() {
        $routes = $this->configRouter['routes'];
        if (Config::getConfig('routerCache')) {
            $dispatcherClass = \FastRoute\cachedDispatcher::class;
            $dispatcherOptions = [
                'cacheFile' => Config::getDirectoryRoot('/cache/router/routesCache.php')
            ];
            if (!is_dir(Config::getDirectoryRoot('/cache/router/'))) {
                mkdir(Config::getDirectoryRoot('/cache/router'));
            }
        } else {
            $dispatcherClass = \FastRoute\simpleDispatcher::class;
            $dispatcherOptions = [];
        }

        $dispatcher = $dispatcherClass(function (\FastRoute\RouteCollector $r) use ($routes) {

            if (Config::getSecurityConfig('authRequired')) {

                $this->setPath('login', '/login');
                $r->get('/login', function () {
                    \BDSCore\Security\Login::renderLogin($this->response, Config::getSecurityConfig('authPage'));
                });
                $r->post('/login', function () {
                    $this->response = \BDSCore\Security\Login::checkForm($this->response);
                });

                $this->setPath('logout', '/logout');
                $r->get('/logout', function () {
                    $_SESSION['auth'] = false;
                    $this->response->withHeader('Location', '/');
                });

            }

            foreach ($routes as $route => $c) {
                $this->setPath($route, $c[1]);
                $exp = explode('@', $c[2]);
                $r->addRoute(strtoupper($c[0]), $c[1], [$this->configRouter['routerConfig']['controllersNamespace'] . '\\' . $exp[0], $exp[1]]);
            }

        }, $dispatcherOptions);

        return $dispatcher;
    }

    /**
     * @return ResponseInterface
     */
    public function run(): ResponseInterface {

        $this->controlPerms();
        $dispatcher = $this->configureDispatcher();

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $routeInfo = $dispatcher->dispatch($httpMethod, $this->getBasePath());

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:

                $this->response->getBody()->write(
                    $this->templateClass->render($this->configRouter['routerConfig']['viewError404'])
                );
                $this->response = $this->response->withStatus(404);

                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:

                // $allowedMethods = $routeInfo[1];

                $this->response->getBody()->write(
                    $this->templateClass->render($this->configRouter['routerConfig']['viewError405'])
                );
                $this->response = $this->response->withStatus(405);

                break;
            case \FastRoute\Dispatcher::FOUND:

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if (is_a($handler, 'Closure')) {
                    $handler();
                } else {
                    $class = new $handler[0]($this->request, $this->response);

                    call_user_func_array([$class, $handler[1]], $this->getArgsByName([$class, $handler[1]], $vars));

                    if (is_callable([$class, 'getResponse'])) {
                        $this->response = call_user_func([$class, 'getResponse']);
                    }
                }

                break;
        }

        return $this->response;
    }

}