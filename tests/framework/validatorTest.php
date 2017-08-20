<?php

use \BDSHelpers\Validator\Validator;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{

    public function testCheckLength() {
        $validation = Validator::checkLength('testString', 3, 10);
        $this->assertTrue($validation);

        $validation = Validator::checkLength('test', 0, 5);
        $this->assertTrue($validation);

        $validation = Validator::checkLength('ggg', 5, 10);
        $this->assertFalse($validation);

        $validation = Validator::checkLength('IsIsATest', 3, 5);
        $this->assertFalse($validation);
    }

    public function testCheckType() {
        $validation = Validator::checkType('testString', 'string');
        $this->assertTrue($validation);

        $validation = Validator::checkType('123', 'string');
        $this->assertTrue($validation);

        $validation = Validator::checkType(123, 'string');
        $this->assertFalse($validation);

        $validation = Validator::checkType(123, 'int');
        $this->assertTrue($validation);

        $validation = Validator::checkType('testOk', 'integger');
        $this->assertFalse($validation);
    }

    public function testCheckFilter() {
        $validation = Validator::checkFilter('test@gmail.com', 'email');
        $this->assertTrue($validation);

        $validation = Validator::checkFilter('test@test', 'email');
        $this->assertFalse($validation);

        $validation = Validator::checkFilter('testtest.fr', 'email');
        $this->assertFalse($validation);

        $validation = Validator::checkFilter('https://test.fr/', 'url');
        $this->assertTrue($validation);

        $validation = Validator::checkFilter('test', 'url');
        $this->assertFalse($validation);

        $validation = Validator::checkFilter('test.fr', 'url');
        $this->assertFalse($validation);
    }

}