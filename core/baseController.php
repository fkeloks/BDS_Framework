<?php

namespace BDSCore;

/**
 * Class BaseController
 * @package BDSCore
 */
class BaseController
{

    /**
     * @var \DI\Container
     */
    private $container;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        $containerBuilder = new \DI\ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $this->container = $containerBuilder->build();

        \BDSCore\Debug\DebugBar::pushElement('RequestMethod', $this->getMethod());
    }

    /**
     * @param $path
     * @param array $args
     */
    public function render($path, $args = []) {
        $template = $this->container->get(\BDSCore\Twig\Template::class);
        echo $template->render($path, $args);
    }

    /**
     * @param string $routeNameOrUrl
     */
    public function redirect(string $routeNameOrUrl) {
        $url = \BDSCore\Router\Router::getPath($routeNameOrUrl);
        ($url != '') ? header('Location:' . $url) : header('Location:' . $routeNameOrUrl);
    }

    /**
     * @return array
     */
    public function getHeaders() {
        $router = $this->container->get(\Bramus\Router\Router::class);

        return $router->getRequestHeaders();
    }

    /**
     * @return string
     */
    public function getMethod() {
        $router = $this->container->get(\Bramus\Router\Router::class);

        return $router->getRequestMethod();
    }

}