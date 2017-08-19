<?php

namespace BDSHelpers\Auth;

/**
 * Auth Class: Control Connection
 * Classe Auth: contrôle la connexion
 *
 * @package BDSHelpers\Auth
 */
class Auth
{

    /**
     * Returns TRUE if the user is logged on, otherwise FALSE
     * Renvoi TRUE si l'utilisateur est connecté, sinon FALSE
     *
     * @return bool True or False
     */
    public static function isLogged(): bool {
        if (!isset($_SESSION['auth'])) {
            $_SESSION['auth'] = false;
        }

        return $_SESSION['auth'];
    }

    /**
     * Connects the user
     * Connecte l'utilisateur
     *
     * @return void
     */
    public static function login() {
        $_SESSION['auth'] = true;
    }

    /**
     * Disconnect the user
     * Désonnecte l'utilisateur
     * 
     * @return void
     */
    public static function logout() {
        $_SESSION['auth'] = false;
    }

}