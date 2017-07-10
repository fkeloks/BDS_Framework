<?php

describe('Forms', function () {

    it('Class Instantiation Test', function () {
        expect(function () {
            new \BDSCore\Forms\Forms();
        })->toThrow();
        expect(function () {
            new \BDSCore\Forms\Forms('get');
        })->toThrow();
        expect(function () {
            new \BDSCore\Forms\Forms('get', []);
        })->toThrow();
        expect(function () {
            new \BDSCore\Forms\Forms('get', [
                'myValue' => 'str'
            ]);
        })->not->toThrow();
    });

    it('Testing the Forms Class: Declaring the Values of a Form', function () {
        expect(function () {
            new \BDSCore\Forms\Forms('get', [
                'name'    => 'str',
                'comment' => [
                    'type' => 'str'
                ]
            ]);
        })->not->toThrow();
        // --------------------------------------
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'randomValue' => 'randomVamlue'
            ]
        ]);
        expect($form->validate())->toThrow();
    });

    it('Testing the Forms Class: Checking the Values of a Form', function () {
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'type'       => 'str',
                'min-length' => 3
            ]
        ]);
        expect($form->validate())->toBeFalsy();
        $_GET['name'] = 'Babar';
        $_GET['comment'] = 1234;
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'MyComment';
        expect($form->validate())->toBeTruthy();
    });

    it('Testing the Forms class: Checking the keys of the parameter table.', function () {
        $_GET['name'] = 'Babar';
        $_GET['comment'] = 'MyCom';
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'type'       => 'str',
                'min-length' => '3',
                'max-length' => '6',
            ]
        ]);
        expect($form->validate())->toBeTruthy();
        // --------------------------------------
        $_GET['comment'] = 'MyComX';
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'value' => 'MyCom',
            ]
        ]);
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'MyCom';
        expect($form->validate())->toBeTruthy();
        // --------------------------------------
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'keyIncludedIn' => [
                    'K1' => 'V1',
                    'K2' => 'V2',
                ],
            ]
        ]);
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'V1';
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'K2';
        expect($form->validate())->toBeTruthy();
        // --------------------------------------
        $_GET['comment'] = 'randomValue';
        $form = new \BDSCore\Forms\Forms('get', [
            'name'    => 'str',
            'comment' => [
                'valueIncludedIn' => [
                    'K1' => 'V1',
                    'K2' => 'V2',
                ],
            ]
        ]);
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'K1';
        expect($form->validate())->toBeFalsy();
        $_GET['comment'] = 'V2';
        expect($form->validate())->toBeTruthy();
    });

});