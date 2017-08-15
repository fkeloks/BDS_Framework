<?php

use \BDSCore\Config\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() {
        \BDSCore\Config\Config::setDirectoryConfig();
    }

    public function testGetAllConfig() {
        $config = Config::getAllConfig();
        $this->assertEquals(15, count($config));
        $this->assertInternalType('array', $config);
    }

    public function TestGetItemOfConfig() {
        $this->assertInternalType('boolean', Config::getConfig('debugFile'));
        $this->assertInternalType('string', Config::getConfig('locale'));

        $this->expectException(\BDSCore\Config\ConfigException::class);
        Config::getConfig('randomValue');
    }

    public function testGetRouterConfig() {
        $this->assertInternalType('string', Config::getRouterConfig('controllersNamespace'));

        $this->expectException(\BDSCore\Config\ConfigException::class);
        Config::getRouterConfig('randomValue');
    }

    public function testGetSecurityConfig() {
        $this->assertInternalType('integer', Config::getSecurityConfig('sessionLifetime'));

        $securityConfig = Config::getAllSecurityConfig();
        $this->assertInternalType('array', $securityConfig);
        $this->assertEquals(2, count($securityConfig));

        $this->expectException(\BDSCore\Config\ConfigException::class);
        Config::getSecurityConfig('randomValue');
    }

}