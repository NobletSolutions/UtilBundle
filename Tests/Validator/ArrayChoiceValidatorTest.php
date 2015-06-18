<?php

namespace NS\UtilBundle\Tests\Validator;

class ArrayChoiceValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
       $validator = new \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator();
       $this->assertFalse($validator->isValid(' '));
       $demoChoice = new DemoArrayChoice();
       $this->assertFalse($validator->isValid($demoChoice));
       $demoChoice->setValue(1);
       $this->assertTrue($validator->isValid($demoChoice));
    }
}
