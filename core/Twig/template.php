<?php

namespace BDSCore\Template;

use \BDSCore\Config\Config;
use \BDSCore\Router\Router;
use \BDSCore\Debug\DebugBar;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Twig
 * @package BDSCore\Template
 */
class Twig
{

    /**
     * @var null|\Twig_Environment
     */
    private $twig = null;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Template constructor.
     * @param ResponseInterface $response
     */
    function __construct(ResponseInterface $response) {
        $this->response = $response;

        $loader = new \Twig_Loader_Filesystem(Config::getConfig('twigViews'));
        $twig = new \Twig_Environment($loader, [
            'cache' => Config::getConfig('twigCache'),
        ]);

        $twig->addFunction(new \Twig_SimpleFunction('assets', function (string $path): string {
            return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, -10) . '/public/' . $path;
        }));
        $twig->addFunction(new \Twig_SimpleFunction('getLocale', function (): string {
            return Config::getConfig('locale');
        }));
        $twig->addFunction(new \Twig_SimpleFunction('path', function(string $routeName = null, array $params = []): string {
            return Router::getPath($routeName, $params);
        }));

        $this->twig = $twig;
    }

    /**
     * @param string $path
     * @param array $args
     */
    public function render(string $path, array $args = []) {
        DebugBar::pushElement('View', $path);
        $file = $this->twig->render($path, $args);
        if(Config::getConfig('debugBar')) {
            $this->response->getBody()->write(DebugBar::insertDebugBar($file));
        } else {
            $this->response->getBody()->write($file);
        }
    }

}