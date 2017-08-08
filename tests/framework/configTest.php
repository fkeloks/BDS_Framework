<?php

use \BDSCore\Config\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testGetAllConfig() {
        $config = Config::getAllConfig();
        $this->assertEquals(14, count($config));
        $this->assertInternalType('array', $config);
    }

    public function TestGetItemOfConfig() {
        $this->assertInternalType('boolean', Config::getConfig('debugFile'));
        $this->assertInternalType('string', Config::getConfig('locale'));
    }

    public function testGetRouterConfig() {
        $this->assertInternalType('string', Config::getRouterConfig('controllersNamespace'));
    }

    public function testGetSecurityConfig() {
        $this->assertInternalType('integer', Config::getSecurityConfig('sessionLifetime'));
        $this->assertInternalType('boolean', Config::getSecurityConfig('authRequired'));
        $this->assertInternalType('array', Config::getSecurityConfig('authAccounts'));

        $securityConfig = Config::getAllSecurityConfig();
        $this->assertInternalType('array', $securityConfig);
        $this->assertEquals(5, count($securityConfig));
    }

}