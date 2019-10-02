<?php

namespace NS\UtilBundle\Tests\Validator;

use \NS\UtilBundle\Validator\Constraints\ArrayChoiceConstraint;
use \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ArrayChoiceValidatorTest extends TestCase
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
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('buildViolation');

        $validator  = new ArrayChoiceValidator();
        $demoChoice = new DemoArrayChoice(1);

        $validator->validate($demoChoice, new ArrayChoiceConstraint());
    }

    public function testValidateNotValid()
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($builder);

        $validator  = new ArrayChoiceValidator();
        $validator->initialize($context);
        $demoChoice = new DemoArrayChoice();

        $validator->validate($demoChoice, new ArrayChoiceConstraint());
    }
}
