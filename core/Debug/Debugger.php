<?php

namespace BDSCore\Debug;

/**
 * Debugger class: Used to debug a variable and the logger
 * Classe Debugger : Permet de déboguer une variable et de la logger
 *
 * @package BDSCore\Debug
 */
class Debugger
{

    /**
     * Debug a variable and logic if "debug Logger" == TRUE in the global configuration file
     * Debug une variable et la logue si "debugLogger" == TRUE dans le fichier de configuration global
     *
     * @param $item Element to debug
     *
     * @return bool
     */
    public static function debug($item): bool {

        if (\BDSCore\Config\Config::getConfig('showDebug')) {
            if (!is_string($item)) {
                echo('<pre>');
                var_dump($item);
                echo('</pre>');
            }
        }

        if (\BDSCore\Config\Config::getConfig('debugLogger')) {
            date_default_timezone_set(\BDSCore\Config\Config::getConfig('timezone'));
            setlocale(LC_TIME, \BDSCore\Config\Config::getConfig('locale'));

            $logger = new \Monolog\Logger('BDS_Framework');
            $logDirectory = \BDSCore\Config\Config::getDirectoryRoot('/storage/logs/debugLogs.log');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler(self::$logDirectory, \Monolog\Logger::DEBUG));

            (!is_string($item)) ? $item = var_export($item, true) : null;
            $logger->debug($item);
        }

        return true;
    }

}