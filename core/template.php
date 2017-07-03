<?php

namespace BDSCore;

/**
 * Class Template
 * @package BDSCore
 */
class Template
{

    /**
     * @var null|\Twig_Environment
     */
    private $twig = null;

    /**
     * Template constructor.
     */
    function __construct() {
        $loader = new \Twig_Loader_Filesystem(\BDSCore\Config::getConfig('twigViews'));
        $twig = new \Twig_Environment($loader, [
            'cache' => \BDSCore\Config::getConfig('twigCache'),
        ]);
        $twig->addFunction(new \Twig_SimpleFunction('assets', function (string $path): string {
            return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, -10) . '/public/' . $path;
        }));
        $twig->addFunction(new \Twig_SimpleFunction('getLocale', function (): string {
            return \BDSCore\Config::getConfig('locale');
        }));
        $twig->addFunction(new \Twig_SimpleFunction('path', function(string $routeName = null, array $params = []): string {
            return \BDSCore\Router::getPath($routeName, $params);
        }));
        $this->twig = $twig;
    }

    /**
     * @param string $path
     * @param array $args
     */
    public function render(string $path, array $args = []) {
        \BDSCore\DebugBar::pushElement('View', $path);
        $file = $this->twig->render($path, $args);
        if(\BDSCore\Config::getConfig('debugBar')) {
            echo \BDSCore\debugBar::insertDebugBar($file);
        } else {
            echo $file;
        }
    }

}