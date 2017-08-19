<?php

namespace BDSCore\Security;

/**
 * Class Security: gene part of the security framework
 * Classe Securité: gène une partie de la securité du framework
 *
 * @package BDSCore\Security
 */
class Security
{

    /**
     * Checks if current IP is banned
     * Vérifie si l'IP actuelle est bannie
     *
     * @param string|null $ip IP Address
     *
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
     * Banned an IP
     * Bannie une IP
     *
     * @param string|null $ip IP address
     *
     * @return void
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
     * Removes an IP from the list of banned IPs
     * Enlève une IP de la liste des IP bannies
     *
     * @param string|null $ip IP address
     *
     * @return void
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

    /**
     * Returns a 403 error if the IP is banned
     * Retourne une erreur 403 si l'IP est bannie
     *
     * @return void
     */
    public function checkPermissions() {
        if ($this->checkIp()) {
            \BDSCore\Errors\Errors::returnError(403);
        }
    }

}