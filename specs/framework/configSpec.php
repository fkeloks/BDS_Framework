<?php

describe('Config', function () {

    it('Config File Recovery Test', function () {
        $config = \BDSCore\Config\Config::getAllConfig();
        expect($config)->toBeA('array');
        expect(count($config))->toBe(13);
    });

    it('Test for recovery of a configuration item', function () {
        expect(\BDSCore\Config\Config::getConfig('debugFile'))->toBeA('boolean');
        expect(\BDSCore\Config\Config::getConfig('locale'))->toBeA('string');
        expect(function () {
            \BDSCore\Config\Config::getConfig('randomValue');
        })->toThrow();
    });

    it('Recovery test for an item in the router configuration', function () {
        expect(function () {
            \BDSCore\Config\Config::getRouterConfig();
        })->toThrow();
        expect(\BDSCore\Config\Config::getRouterConfig('controllersNamespace'))->toBeA('string');
        expect(function () {
            \BDSCore\Config\Config::getRouterConfig('randomValue');
        })->toThrow();
    });

    it('Recovery test for an item in the security configuration', function () {
        expect(function () {
            \BDSCore\Config\Config::getSecurityConfig();
        })->toThrow();
        expect(\BDSCore\Config\Config::getSecurityConfig('sessionLifetime'))->toBeA('integer');
        expect(\BDSCore\Config\Config::getSecurityConfig('authRequired'))->toBeA('boolean');
        expect(\BDSCore\Config\Config::getSecurityConfig('ipBan'))->toBeA('array');
        expect(function () {
            \BDSCore\Config\Config::getSecurityConfig('randomValue');
        })->toThrow();
        expect(\BDSCore\Config\Config::getAllSecurityConfig())->toBeA('array');
    });

});