<?php

namespace NS\UtilBundle\Tests\Validator;

use \NS\UtilBundle\Validator\Constraints\ArrayChoiceConstraint;
use \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator;

class ArrayChoiceValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
        $validator  = new ArrayChoiceValidator();
        $this->assertFalse($validator->isValid(' '));
        $demoChoice = new DemoArrayChoice();
        $this->assertFalse($validator->isValid($demoChoice));
        $demoChoice->setValue(1);
        $this->assertTrue($validator->isValid($demoChoice));
    }

    public function testValidateIsValid()
    {
        $context = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->never())
            ->method('buildViolation');

        $validator  = new ArrayChoiceValidator();
        $demoChoice = new DemoArrayChoice(1);

        $validator->validate($demoChoice, new ArrayChoiceConstraint());
    }

    /**
     * @param $value
     *
     * @dataProvider getInvalid
     */
    public function testValidateNotValid($value)
    {
        $constraint = new ArrayChoiceConstraint();
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message);

        $validator  = new ArrayChoiceValidator();
        $validator->initialize($context);
        $validator->validate($value, $constraint);
    }

    public function getInvalid()
    {
        return array(
            array(new DemoArrayChoice()),
            array(null),
            array('')
        );
    }
}
