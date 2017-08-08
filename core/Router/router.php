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

        $this->configRouter = include('./config/router.php');
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
     * @return ResponseInterface
     */
    public function run(): ResponseInterface {
        $routes = $this->configRouter['routes'];
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) use ($routes) {

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

        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
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
                    call_user_func([$class, $handler[1]], $vars);

                    if (is_callable([$class, 'getResponse'])) {
                        $this->response = call_user_func([$class, 'getResponse']);
                    }
                }

                break;
        }

        return $this->response;
    }

}