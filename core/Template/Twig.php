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

        $twigView = Config::getDirectoryRoot('/' . Config::getConfig('twigViews'));
        $twigCache = (!Config::getConfig('twigCache')) ? false : Config::getDirectoryRoot('/' . Config::getConfig('twigCache'));
        $loader = new \Twig_Loader_Filesystem($twigView);
        $twig = new \Twig_Environment($loader, [
            'cache' => $twigCache,
        ]);

        $twig->addFunction(new \Twig_SimpleFunction('assets', function (string $path): string {
            try {
                return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, -10) . '/' . $path;
            } catch (\Exception $e) {
                return '';
            }
        }));
        $twig->addFunction(new \Twig_SimpleFunction('getLocale', function (): string {
            return Config::getConfig('locale');
        }));
        $twig->addFunction(new \Twig_SimpleFunction('path', function (string $routeName = null, array $params = []): string {
            return Router::getPath($routeName, $params);
        }));

        $this->twig = $twig;
    }

    /**
     * @param string $path
     * @param array $args
     * @return ResponseInterface
     */
    public function render(string $path, array $args = []) {
        DebugBar::pushElement('View', $path);
        $file = $this->twig->render($path, $args);
        if (Config::getConfig('debugBar')) {
            $this->response->getBody()->write(DebugBar::insertDebugBar($file));
        } else {
            $this->response->getBody()->write($file);
        }

        return $this->response;
    }

}