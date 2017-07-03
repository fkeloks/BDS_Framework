<?php

describe('Config', function() {

    it('Config File Recovery Test', function() {
        $config = \BDSCore\Config::getAllConfig();
        expect($config)->toBeA('array');
        expect(count($config))->toBe(12);
    });

    it('Test for recovery of a configuration item', function() {
        expect(\BDSCore\Config::getConfig('debugFile'))->toBeA('boolean');
        expect(\BDSCore\Config::getConfig('locale'))->toBeA('string');
        expect(\BDSCore\Config::getConfig('randomValue'))->toBeFalsy();
    });

    it('Recovery test for an item in the router configuration', function() {
        expect(\BDSCore\Config::getRouterConfig())->toBeFalsy();
        expect(\BDSCore\Config::getRouterConfig('controllersNamespace'))->toBeA('string');
        expect(\BDSCore\Config::getRouterConfig('randomValue'))->toBeFalsy();
    });

});