<?php

use \BDSHelpers\Observer\Observer;

class ObserverTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() {
        if (session_status() == PHP_SESSION_DISABLED) {
            session_start();
        }
    }

    public function testGetUniqueInstanceOfObserver() {
        $observer = Observer::getObserver();
        $this->assertInstanceOf(Observer::class, $observer);
        $this->assertEquals($observer, Observer::getObserver());
    }

    public function testDeclareEvent() {
        $observer = Observer::getObserver(true);
        $_SESSION['test'] = false;
        $observer->on('bds.test', function () {
            $_SESSION['test'] = true;
        });

        $this->assertFalse($_SESSION['test']);
    }

    public function testEmit() {
        $observer = Observer::getObserver(true);
        $_SESSION['test'] = false;
        $observer->on('bds.test', function () {
            $_SESSION['test'] = true;
        });

        $this->assertFalse($_SESSION['test']);
        $observer->emit('bds.test');
        $this->assertTrue($_SESSION['test']);
    }

    public function testEmitWithArgs() {
        $observer = Observer::getObserver(true);
        $_SESSION['test'] = false;
        $observer->on('bds.test', function ($item) {
            $_SESSION['test'] = $item;
        });

        $this->assertFalse($_SESSION['test']);
        $observer->emit('bds.test', 'OK');
        $this->assertEquals($_SESSION['test'], 'OK');
    }

}