<?php

namespace BDSCore\Config;

/**
 * Class Config
 * @package BDSCore\Config
 */
class Config
{

    private static $configDirectory;

    public static function setDirectoryConfig() {
        self::$configDirectory = '../config';
    }

    /**
     * @return array
     */
    public static function getAllConfig() {
        if (isset($_SESSION)) {
            return $_SESSION['config'];
        } else {
            $config = include(self::$configDirectory . '/config.php');

            if (!is_array($config)) {
                throw new ConfigException('Unable to retrieve site global configuration.');
            }

            return $config;
        }
    }

    /**
     * @param string|null $element
     * @return mixed
     * @throws ConfigException
     */
    public static function getConfig(string $element = null) {
        if ($element != null) {
            if (isset($_SESSION['config'])) {
                if (isset($_SESSION['config'][$element])) {
                    return $_SESSION['config'][$element];
                } else {
                    throw new ConfigException('The "' . $_SESSION['config'][$element] . 'element does not appear to be present in the session.');
                }
            } else {
                $config = include(self::$configDirectory . '/config.php');

                if (isset($config[$element])) {
                    return $config[$element];
                } else {
                    throw new ConfigException('The "' . $element . '" element does not appear to be present in the configuration file.');
                }
            }
        } else {
            throw new ConfigException('The name of an element must be specified in the getConfig() function.');
        }
    }

    /**
     * @return array
     * @throws ConfigException
     */
    public static function getAllRouterConfig(): array {
        $config = include(self::$configDirectory . '/router.php');
        if (!is_array($config)) {
            throw new ConfigException('Unable to retrieve site router configuration.');
        }

        return $config;
    }

    /**
     * @param string|null $element
     * @throws ConfigException
     */
    public static function getRouterConfig(string $element = null) {
        if ($element != null) {
            $config = include(self::$configDirectory . '/router.php');

            if (isset($config['routerConfig'][$element])) {
                return $config['routerConfig'][$element];
            } else {
                throw new ConfigException('The "' . $config['routerConfig'][$element] . '" element does not appear to be present in the configuration file.');
            }
        } else {
            throw new ConfigException('The name of an element must be specified in the getRouterConfig() function.');
        }
    }

    /**
     * @return array
     * @throws ConfigException
     */
    public static function getAllSecurityConfig(): array {
        $config = include(self::$configDirectory . '/security.php');
        if (!is_array($config)) {
            throw new ConfigException('Unable to retrieve site security configuration.');
        }

        return $config;
    }

    /**
     * @param string|null $element
     * @throws ConfigException
     */
    public static function getSecurityConfig(string $element = null) {
        if ($element != null) {
            $config = include(self::$configDirectory . '/security.php');

            if (isset($config[$element])) {
                return $config[$element];
            } else {
                throw new ConfigException('The "' . $config[$element] . '" element does not appear to be present in the configuration file.');
            }
        } else {
            throw new ConfigException('The name of an element must be specified in the getSecurityConfig() function.');
        }
    }

}