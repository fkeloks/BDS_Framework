<?php

namespace BDSCore\Security;

/**
 * Class Security
 * @package BDSCore\Security
 */
class Security
{

    /**
     * @var array
     */
    private $ipBan = [];

    public function __construct(array $ipBan = []) {
        $this->ipBan = $ipBan;
    }

    /**
     * @return bool
     */
    public function checkIp() {
        if (in_array($_SERVER['REMOTE_ADDR'], $this->ipBan)) {
            return true;
        }

        return false;
    }

    public function banIp() {
        array_push($this->ipBan, $_SERVER['REMOTE_ADDR']);
    }

    public function allowIp() {
        $key = array_search($_SERVER['REMOTE_ADDR'], $this->ipBan);
        if ($key !== false) {
            unset($this->ipBan[$key]);
        }
    }

    /**
     * @param $errorCode
     */
    private function returnError($errorCode) {
        try {
            \BDSCore\Errors::returnError($errorCode);
        } catch (\Exception $e) {
            die('-[ Not allowed -]');
        }
    }

    public function checkPermissions() {
        if ($this->checkIp()) {
            $this->returnError(403);
        }
    }

}