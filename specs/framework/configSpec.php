<?php

describe('Config', function() {

    it('Config File Recovery Test', function() {
        $config = \BDSCore\Config\Config::getAllConfig();
        expect($config)->toBeA('array');
        expect(count($config))->toBe(13);
    });

    it('Test for recovery of a configuration item', function() {
        expect(\BDSCore\Config\Config::getConfig('debugFile'))->toBeA('boolean');
        expect(\BDSCore\Config\Config::getConfig('locale'))->toBeA('string');
        expect(\BDSCore\Config\Config::getConfig('randomValue'))->toBeFalsy();
    });

    it('Recovery test for an item in the router configuration', function() {
        expect(\BDSCore\Config\Config::getRouterConfig())->toBeFalsy();
        expect(\BDSCore\Config\Config::getRouterConfig('controllersNamespace'))->toBeA('string');
        expect(\BDSCore\Config\Config::getRouterConfig('randomValue'))->toBeFalsy();
    });

});