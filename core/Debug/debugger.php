<?php

namespace BDSCore\Debug;

/**
 * Class Debugger
 * @package BDSCore\Debug
 */
class Debugger
{

    /**
     * @var string
     */
    private static $directoryLogs = './storage/logs/';

    /**
     * @param $item
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