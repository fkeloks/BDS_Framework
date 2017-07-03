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

        \BDSCore\DebugBar::pushElement('RequestMethod', $this->getMethod());
    }

    /**
     * @param $path
     * @param array $args
     */
    public function render($path, $args = []) {
        $template = $this->container->get(\BDSCore\Template::class);
        echo $template->render($path, $args);
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