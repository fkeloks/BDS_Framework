<?php

namespace BDSCore;

/**
 * Class Debugger
 * @package BDSCore
 */
class Debugger
{

    /**
     * @var string
     */
    private $directoryLogs = './storage/logs/';

    /**
     * @param $item
     * @return bool
     */
    public function debug($item): bool {

        if (!is_string($item)) {
            echo('<pre>');
            var_dump($item);
            echo('</pre>');
        }

        if (\BDSCore\Config::getConfig('debugFile')) {
            date_default_timezone_set(\BDSCore\Config::getConfig('timezone'));
            setlocale(LC_TIME, \BDSCore\Config::getConfig('locale'));
            $date = strftime('%A %d %B %Y, %H:%M:%S');

            if (is_string($item) || is_array($item) || is_bool($item)) {
                $dump = var_export($item, true);
            } else {
                $dump = print_r($item, true);
            }

            $log = "\n--------------------------------------------------------------------------------------------------\nBDSDebug :: {$date}\n{$dump}";

            file_put_contents($this->directoryLogs . 'debugLogs.txt', $log, FILE_APPEND);
        }

        return true;
    }

}