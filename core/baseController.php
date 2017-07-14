<?php

namespace BDSCore;

/**
 * Class BaseController
 * @package BDSCore
 */
class BaseController
{

    /**
     * @var Twig\Template
     */
    private $templateClass;
    /**
     * @var \Bramus\Router\Router
     */
    private $routerClass;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        $this->templateClass = new \BDSCore\Twig\Template();
        $this->routerClass = new \Bramus\Router\Router();

        \BDSCore\Debug\DebugBar::pushElement('RequestMethod', $this->getMethod());
    }

    /**
     * @param $path
     * @param array $args
     */
    public function render($path, $args = []) {
        $template = new $this->templateClass;
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
        $router = $this->routerClass;

        return $router->getRequestHeaders();
    }

    /**
     * @return string
     */
    public function getMethod() {
        $router = $this->routerClass;

        return $router->getRequestMethod();
    }

}