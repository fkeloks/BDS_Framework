<?php

namespace BDSCore;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseController
 * @package BDSCore
 */
class BaseController
{

    /**
     * @var Template\Twig
     */
    private $templateClass;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * BaseController constructor.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response) {
        $this->request = $request;
        $this->response = $response;

        $this->templateClass = new \BDSCore\Template\Twig($response);
    }

    /**
     * @param string $path
     * @param array $args
     * @return void
     */
    public function render(string $path, array $args = []) {
        $this->templateClass->render($path, $args);
    }

    /**
     * @param string $className
     * @param array ...$args
     * @return bool|object
     */
    public function call(string $className, ...$args) {
        $classList = [
            'form'     => \BDSCore\Form\Form::class,
            'config'   => \BDSCore\Config\Config::class,
            'database' => \BDSCore\Database\Database::class,
            'observer' => \BDSCore\Observer\Observer::class,
            'debugbar' => \BDSCore\Debug\DebugBar::class,
            'errors'   => \BDSCore\Errors\Errors::class
        ];
        $className = strtolower($className);
        if (array_key_exists($className, $classList)) {
            if (empty($args)) {
                $class = new $classList[$className]();
            } else {
                $refClass = new \ReflectionClass($classList[$className]);
                $class = $refClass->newInstanceArgs($args);
            }

            return $class;
        }

        return false;
    }

    /**
     * @param string $item
     * @return mixed
     */
    public function getGlobalConfig(string $item) {
        return \BDSCore\Config\Config::getConfig($item);
    }

    /**
     * @param string $item
     * @return mixed
     */
    public function getRouterConfig(string $item) {
        return \BDSCore\Config\Config::getRouterConfig($item);
    }

    /**
     * @param string $item
     * @return mixed
     */
    public function getSecurityConfig(string $item) {
        return \BDSCore\Config\Config::getSecurityConfig($item);
    }

    /**
     * @param string $routeNameOrUrl
     * @return void
     */
    public function redirect(string $routeNameOrUrl) {
        $url = \BDSCore\Router\Router::getPath($routeNameOrUrl);
        $this->response = ($url != '') ? $this->response->withHeader('Location', $url) : $this->response->withHeader('Location', $routeNameOrUrl);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function setResponse(ResponseInterface $response): ResponseInterface {
        $this->response = $response;

        return $this->response;
    }

    /**
     * @param string $header
     * @return \string[]
     */
    public function getHeader(string $header) {
        return $this->response->getHeader($header);
    }

    /**
     * @return \string[][]
     */
    public function getHeaders() {
        return $this->request->getHeaders();
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->request->getMethod();
    }

}