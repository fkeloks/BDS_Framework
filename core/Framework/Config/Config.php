<?php

namespace BDSCore\Config;

/**
 * Managing framework configuration files
 * Gestion des fichiers de configuration du framework
 *
 * @package BDSCore\Config
 */
class Config
{

    /**
     * @var string Directory path for config
     */
    private static $configDirectory;

    /**
     * Defines the root directory of the framework
     * Définit le repertoire racine du framework
     *
     * @return void
     */
    public static function setDirectoryConfig() {
        if (is_dir('../config')) {
            self::$configDirectory = '..';
        } else {
            self::$configDirectory = '.';
        }
    }

    /**
     * Retrieves the root directory of the framework
     * Récupère le repertoire racine du framework
     *
     * @param string $directory
     *
     * @return string
     */
    public static function getDirectoryRoot(string $directory): string {
        return self::$configDirectory . $directory;
    }

    /**
     * Recovers the global configuration of the framework
     * Récupère la configuration globale du framework
     *
     * @return array
     * @throws ConfigException
     */
    public static function getAllConfig(): array {
        if (isset($_SESSION['config'])) {
            return $_SESSION['config'];
        } else {
            $config = include(self::$configDirectory . '/config/config.php');

            if (!is_array($config)) {
                throw new ConfigException('Unable to retrieve site global configuration.');
            }

            return $config;
        }
    }

    /**
     * Recovers an element of the global configuration of the framework
     * Récupère un élement de la configuration globale du framework
     *
     * @param string|null $element Element to recover
     *
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
                $config = include(self::$configDirectory . '/config/config.php');

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
     * Retrieves the router configuration
     * Récupère la configuration du router
     *
     * @return array
     * @throws ConfigException
     */
    public static function getAllRouterConfig(): array {
        $config = include(self::$configDirectory . '/config/router.php');
        if (!is_array($config)) {
            throw new ConfigException('Unable to retrieve site router configuration.');
        }

        return $config;
    }

    /**
     * Retrieves an item from the router configuration
     * Récupère un élement de la configuration du router
     *
     * @param string|null $element Element to recover
     *
     * @return mixed
     * @throws ConfigException
     */
    public static function getRouterConfig(string $element = null) {
        if ($element != null) {
            $config = include(self::$configDirectory . '/config/router.php');

            if (isset($config['routerConfig'][$element])) {
                return $config['routerConfig'][$element];
            } else {
                throw new ConfigException('The "' . $element . '" element does not appear to be present in the configuration file.');
            }
        } else {
            throw new ConfigException('The name of an element must be specified in the getRouterConfig() function.');
        }
    }

    /**
     * Retrieves security configuration
     * Récupère la configuration de la sécurité
     *
     * @return array
     * @throws ConfigException
     */
    public static function getAllSecurityConfig(): array {
        $config = include(self::$configDirectory . '/config/security.php');
        if (!is_array($config)) {
            throw new ConfigException('Unable to retrieve site security configuration.');
        }

        return $config;
    }

    /**
     * Recovers an item from the security configuration
     * Récupère un élement de la configuration de la sécurité
     *
     * @param string|null $element Element to recover
     *
     * @return mixed
     * @throws ConfigException
     */
    public static function getSecurityConfig(string $element = null) {
        if ($element != null) {
            $config = include(self::$configDirectory . '/config/security.php');

            if (isset($config[$element])) {
                return $config[$element];
            } else {
                throw new ConfigException('The "' . $element . '" element does not appear to be present in the configuration file.');
            }
        } else {
            throw new ConfigException('The name of an element must be specified in the getSecurityConfig() function.');
        }
    }

}