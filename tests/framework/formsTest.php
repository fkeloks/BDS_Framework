<?php

use \BDSCore\Form\Form;

class FormTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() {
        \BDSCore\Config\Config::setDirectoryConfig();
    }

    public function testInstanceOfForm() {
        $form = new Form('get');
        $this->assertInstanceOf(Form::class, $form);
        $form = new Form('post');
        $this->assertInstanceOf(Form::class, $form);
    }

    public function testTypeOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => 'string'
        ]);
        $_GET['item'] = 123;
        $this->assertFalse($form->validate());

        $form->configure([
            'item' => 'int'
        ]);
        $_GET['item'] = 'Bob';
        $this->assertFalse($form->validate());

        $_GET['item'] = 'Bob';
        $form->configure([
            'item' => 'string'
        ]);
        $this->assertTrue($form->validate());
    }

    public function testValueOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'value' => 'Bob'
            ]
        ]);

        $_GET['item'] = 'Yeah';
        $this->assertFalse($form->validate());

        $_GET['item'] = 123;
        $this->assertFalse($form->validate());

        $_GET['item'] = 'Bob';
        $this->assertTrue($form->validate());
    }

    public function testMinLengthOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'min-length' => 3
            ]
        ]);

        $_GET['item'] = 'RandomValue';
        $this->assertTrue($form->validate());

        $_GET['item'] = 'Ra';
        $this->assertFalse($form->validate());

        $_GET['item'] = '';
        $this->assertFalse($form->validate());
    }

    public function testMaxLengthOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'max-length' => 5
            ]
        ]);

        $_GET['item'] = 'RandomValue';
        $this->assertFalse($form->validate());

        $_GET['item'] = 'Ra';
        $this->assertTrue($form->validate());

        $_GET['item'] = '';
        $this->assertTrue($form->validate());
    }

    public function testKeyInItem() {
        $array = [
            'K1' => 'V',
            'K2' => [
                'K3'
            ],
            'K4'
        ];
        $form = new Form('get');
        $form->configure([
            'item' => [
                'keyIncludedIn' => $array
            ]
        ]);

        $_GET['item'] = 'K0';
        $this->assertFalse($form->validate());

        $_GET['item'] = 'K1';
        $this->assertTrue($form->validate());

        $_GET['item'] = 'K2';
        $this->assertTrue($form->validate());

        $_GET['item'] = 'K3';
        $this->assertFalse($form->validate());

        $_GET['item'] = 'K4';
        $this->assertFalse($form->validate());
    }

}