<?php

namespace BDSCore\Security;

/**
 * Class Auth
 * @package BDSCore\Security
 */
class Auth
{

    /**
     * @return bool
     */
    public static function isLogged(): bool {
        if (!isset($_SESSION['auth'])) {
            $_SESSION['auth'] = false;
        }

        return $_SESSION['auth'];
    }

    /**
     * @return void
     */
    public static function login() {
        $_SESSION['auth'] = true;
    }

    /**
     * @return void
     */
    public static function logout() {
        $_SESSION['auth'] = false;
    }

}