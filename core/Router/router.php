<?php

namespace BDSCore\Router;

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
     * Router constructor.
     */
    public function __construct() {
        $this->routerClass = new \Bramus\Router\Router();
        $this->configRouter = include('./config/router.php');
        $this->templateClass = new \BDSCore\Twig\Template();
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
     * @return bool
     */
    public function run(): bool {
        foreach ($this->configRouter['routes'] as $route => $c) {
            $this->setPath($route, $c[1]);
            call_user_func([$this->routerClass, $c[0]], $c[1], $this->configRouter['routerConfig']['controllersNamespace'] . '\\' . $c[2]);
        }

        $template = $this->templateClass;
        $this->routerClass->set404(function () use ($template) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo $template->render($this->configRouter['routerConfig']['viewError404']);
        });

        $this->routerClass->run();

        return true;
    }

}