<?php

namespace BDSCore;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * BaseController
 * Controller de base
 *
 * @package BDSCore
 */
class BaseController
{

    /**
     * @var Template\Twig Twig engine
     */
    private $templateClass;

    /**
     * @var RequestInterface Request
     */
    private $request;

    /**
     * @var ResponseInterface Response
     */
    private $response;

    /**
     * BaseController constructor.
     * Constructeur de la classe
     *
     * @param RequestInterface $request Request
     * @param ResponseInterface $response Response
     *
     * @return void
     */
    public function __construct(RequestInterface $request, ResponseInterface $response) {
        $this->request = $request;
        $this->response = $response;

        $this->templateClass = new \BDSCore\Template\Twig($response);
    }

    /**
     * Makes a view via Twig
     * Rend une vue via Twig
     *
     * @param string $path
     * @param array $args
     *
     * @return ResponseInterface Response
     */
    public function render(string $path, array $args = []): ResponseInterface {
        return $this->templateClass->render($path, $args);
    }

    /**
     * Simplified call to a framework class
     * Appel simplifié à une classe du framework
     *
     * @param string $className Name of class
     * @param array ...$args Arguments for constructor
     *
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
     * Returns a configuration element resulting from the global configuration of the framework
     * Retourne un élement de configuration issue de la configuration globale du framework
     *
     * @param string $item Element
     *
     * @return mixed
     */
    public function getGlobalConfig(string $item) {
        return \BDSCore\Config\Config::getConfig($item);
    }

    /**
     * Returns a configuration item from the router configuration
     * Retourne un élement de configuration issue de la configuration du routeur
     *
     * @param string $item Element
     *
     * @return mixed
     */
    public function getRouterConfig(string $item) {
        return \BDSCore\Config\Config::getRouterConfig($item);
    }

    /**
     * Returns a configuration item from the framework's security configuration
     * Retourne un élement de configuration issue de la configuration de la sécurité du framework
     *
     * @param string $item Element
     *
     * @return mixed
     */
    public function getSecurityConfig(string $item) {
        return \BDSCore\Config\Config::getSecurityConfig($item);
    }

    /**
     * Redirects to a route or to a URL
     * Redirige vers une route ou vers une URL
     *
     * @param string $routeNameOrUrl Route name or URL
     *
     * @return void
     */
    public function redirect(string $routeNameOrUrl) {
        $url = \BDSCore\Router\Router::getPath($routeNameOrUrl);
        $this->response = ($url != '') ? $this->response->withHeader('Location', $url) : $this->response->withHeader('Location', $routeNameOrUrl);
    }

    /**
     * Returns the request
     * Retourne la requête
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }

    /**
     * Returns the response
     * Retourne la réponse
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface {
        return $this->response;
    }

    /**
     * Updates the response
     * Actualise la réponse
     *
     * @param ResponseInterface $response Response
     *
     * @return ResponseInterface Updated Response
     */
    public function setResponse(ResponseInterface $response): ResponseInterface {
        $this->response = $response;

        return $this->response;
    }

    /**
     * Returns a response header
     * Retourne un entête de la réponse
     *
     * @param string $header Header name
     *
     * @return \string[] Header
     */
    public function getHeader(string $header) {
        return $this->response->getHeader($header);
    }

    /**
     * Returns the headers of the response
     * Retourne les entêtes de la réponse
     *
     * @return \string[][] Headers
     */
    public function getHeaders() {
        return $this->request->getHeaders();
    }

    /**
     * Returns the current method
     * Retourne la méthode actuelle
     *
     * @return string
     */
    public function getMethod(): string {
        return $this->request->getMethod();
    }

}