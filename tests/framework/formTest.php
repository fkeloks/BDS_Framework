<?php

use \BDSHelpers\Form\Form;

class FormTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() {
        \BDSCore\Config\Config::setDirectoryConfig();
    }

    private function getRequest() {
        return \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
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
            'item' => 'str'
        ]);
        $_GET['item'] = 123;
        $this->assertFalse($form->validate($this->getRequest()));

        $form->configure([
            'item' => 'int'
        ]);
        $_GET['item'] = 'Bob';
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 'Bob';
        $form->configure([
            'item' => 'str'
        ]);
        $this->assertTrue($form->validate($this->getRequest()));
    }

    public function testValueOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'value' => 'Bob'
            ]
        ]);

        $_GET['item'] = 'Yeah';
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 123;
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 'Bob';
        $this->assertTrue($form->validate($this->getRequest()));
    }

    public function testMinLengthOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'min-length' => 3
            ]
        ]);

        $_GET['item'] = 'RandomValue';
        $this->assertTrue($form->validate($this->getRequest()));

        $_GET['item'] = 'Ra';
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = '';
        $this->assertFalse($form->validate($this->getRequest()));
    }

    public function testMaxLengthOfItem() {
        $form = new Form('get');
        $form->configure([
            'item' => [
                'max-length' => 5
            ]
        ]);

        $_GET['item'] = 'RandomValue';
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 'Ra';
        $this->assertTrue($form->validate($this->getRequest()));

        $_GET['item'] = '';
        $this->assertTrue($form->validate($this->getRequest()));
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
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 'K1';
        $this->assertTrue($form->validate($this->getRequest()));

        $_GET['item'] = 'K2';
        $this->assertTrue($form->validate($this->getRequest()));

        $_GET['item'] = 'K3';
        $this->assertFalse($form->validate($this->getRequest()));

        $_GET['item'] = 'K4';
        $this->assertFalse($form->validate($this->getRequest()));
    }

    public function testValueInArray() {
        $array = [
            'K1' => 'V',
            'K2' => [
                'K3'
            ],
            'K4'
        ];
        $form = new Form('post');
        $form->configure([
            'item' => [
                'in_array' => $array
            ]
        ]);

        $_POST['item'] = 'K0';
        $this->assertFalse($form->validate($this->getRequest()));

        $_POST['item'] = 'K1';
        $this->assertFalse($form->validate($this->getRequest()));

        $_POST['item'] = 'K2';
        $this->assertFalse($form->validate($this->getRequest()));

        $_POST['item'] = 'V';
        $this->assertTrue($form->validate($this->getRequest()));

        $_POST['item'] = 'K3';
        $this->assertFalse($form->validate($this->getRequest()));

        $_POST['item'] = 'K4';
        $this->assertTrue($form->validate($this->getRequest()));
    }

}