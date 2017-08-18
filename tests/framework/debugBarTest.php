<?php

use \BDSCore\Debug\DebugBar;

class DebugBarTest extends \PHPUnit\Framework\TestCase
{

    public function testPushElement() {
        DebugBar::pushElement('key', 'Value');
        DebugBar::pushElement('key2', ['Value']);
        DebugBar::pushElement('key5', true);
        DebugBar::pushElement('key6', 123);

        $this->assertInternalType('array', DebugBar::getElements());
        $this->assertCount(4, DebugBar::getElements());
    }

    public function testFailPushElement() {
        $this->expectException(\BDSCore\Debug\DebugException::class);
        DebugBar::pushElement('key3', new \stdClass());

        $this->expectException(\BDSCore\Debug\DebugException::class);
        DebugBar::pushElement('key4', function () {
            return 'Nope';
        });
    }

}