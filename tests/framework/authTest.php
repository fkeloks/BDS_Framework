<?php

use \BDSHelpers\Auth\Auth;

class AuthTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() {
        if (session_status() == PHP_SESSION_DISABLED) {
            session_start();
        }
    }

    public function testCheckAuthIfNotLogged() {
        $this->assertFalse(Auth::isLogged());
    }

    public function testLoginUser() {
        Auth::login();
        $this->assertTrue($_SESSION['auth']);
    }

    public function testCheckAuthIfIsLogged() {
        $this->assertTrue(Auth::isLogged());
    }

    public function testLogoutUser() {
        Auth::logout();
        $this->assertFalse($_SESSION['auth']);
    }
}