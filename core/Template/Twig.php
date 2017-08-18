<?php

namespace BDSCore\Template;

use \BDSCore\Config\Config;
use \BDSCore\Router\Router;
use \BDSCore\Debug\DebugBar;
use Psr\Http\Message\ResponseInterface;

/**
 * Twig Class: Twig template engine
 * Classe Twig: moteur de template Twig
 *
 * @package BDSCore\Template
 */
class Twig
{

    /**
     * @var \Twig_Environment Twig engine
     */
    private $twig = null;

    /**
     * @var ResponseInterface Response
     */
    private $response;

    /**
     * Twig constructor.
     * Constructeur de la classe
     *
     * @param ResponseInterface $response Response
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
     * Makes a view via Twig
     * Rend une vue via Twig
     *
     * @param string $path Path
     * @param array $args Arguments passeds to view
     *
     * @return ResponseInterface Response
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