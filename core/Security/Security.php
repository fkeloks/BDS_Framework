<?php

namespace BDSCore\Security;

/**
 * Class Security
 * @package BDSCore\Security
 */
class Security
{

    /**
     * @param string|null $ip
     * @return bool
     */
    public function checkIp(string $ip = null): bool {
        $ip = (!is_null($ip)) ? $ip : $_SERVER['REMOTE_ADDR'];
        $ipBan = json_decode(file_get_contents('./storage/framework/IPBanners.json'));
        if (in_array($ip, $ipBan)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $ip
     */
    public function banIp(string $ip = null) {
        $ip = (!is_null($ip)) ? $ip : $_SERVER['REMOTE_ADDR'];
        $ipBan = json_decode(file_get_contents('./storage/framework/IPBanners.json'));
        if (!in_array($ip, $ipBan)) {
            array_push($ipBan, $ip);
        }
        file_put_contents('./storage/framework/IPBanners.json', json_encode($ipBan));
    }

    /**
     * @param string|null $ip
     */
    public function allowIp(string $ip = null) {
        $ip = (!is_null($ip)) ? $ip : $_SERVER['REMOTE_ADDR'];
        $ipBan = json_decode(file_get_contents('./storage/framework/IPBanners.json'));
        $key = array_search($ip, $ipBan);
        if ($key !== false) {
            unset($ipBan[$key]);
            file_put_contents('./storage/framework/IPBanners.json', json_encode($ipBan));
        }
    }

    public function checkPermissions() {
        if ($this->checkIp()) {
            \BDSCore\Errors\Errors::returnError(403);
        }
    }

}