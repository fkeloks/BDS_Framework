<?php

namespace BDSCore;

/**
 * Class Logger
 * @package BDSCore
 */
class Logger
{

    /**
     * @var string
     */
    private $directoryLogs = './storage/logs/';

    /**
     * @param $str
     * @return bool
     */
    public function log($str): bool {

        date_default_timezone_set(\BDSCore\Config::getConfig('timezone'));
        setlocale(LC_TIME, \BDSCore\Config::getConfig('locale'));

        $date = strftime('%A %d %B %Y, %H:%M:%S');

        if (is_a($str, 'Exception')) {
            try {
                $str = 'In :: ' . $str->getFile() . ' (Line ' . $str->getLine() . ")\n" . get_class($str) . ' :: ' . $str->getMessage();

                $log = "\n--------------------------------------------------------------------------------------------------\nBDSDebug :: {$date}\n{$str}";

                file_put_contents($this->directoryLogs . 'frameworkLogs.txt', $log, FILE_APPEND);
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

}