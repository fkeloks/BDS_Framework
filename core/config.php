<?php

namespace BDSCore;

/**
 * Class Config
 * @package BDSCore
 */
class Config
{

    /**
     * @return array
     */
    public static function getAllConfig(): array {
        if (isset($_SESSION)) {
            return $_SESSION['config'];
        } else {
            $config = include('config/config.php');

            return $config;
        }
    }

    /**
     * @param string|null $element
     * @return bool
     */
    public static function getConfig(string $element = null) {
        if ($element != null) {
            if (isset($_SESSION['config'])) {
                return (isset($_SESSION['config'][$element])) ? $_SESSION['config'][$element] : false;
            } else {
                $config = include('config/config.php');

                return (isset($config[$element])) ? $config[$element] : false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string|null $element
     * @return bool
     */
    public static function getRouterConfig(string $element = null) {
        if($element != null) {
            $config = include('config/router.php');

            return (isset($config['routerConfig'][$element])) ? $config['routerConfig'][$element] : false;
        } else {
            return false;
        }
    }

}