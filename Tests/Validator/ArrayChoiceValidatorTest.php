<?php

namespace NS\UtilBundle\Tests\Validator;

class ArrayChoiceValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
        $validator  = new \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator();
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

        $validator  = new \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator();
        $demoChoice = new DemoArrayChoice(1);

        $validator->validate($demoChoice, new \NS\UtilBundle\Validator\Constraints\ArrayChoice());
    }

    public function testValidateNotValid()
    {
        $builder = $this->getMockBuilder('\Symfony\Component\Validator\Context\ConstraintViolationBuilderInterface')
            ->setMethods(array('atPath','setParameter','setParameters','setTranslationDomain','setInvalidValue','setPlural','setCode','setCause','addViolation',))
            ->disableOriginalConstructor()
            ->getMock();
        $builder->expects($this->once())
            ->method('addViolation');

        $context = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->setMethods(array('buildViolation','addViolation','addViolationAt','validate','validateValue','getViolations','getRoot','getMetadata','getValue','getClassName','getGroup','getMetadataFactory','getPropertyName','getPropertyPath'))
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($builder);

        $validator  = new \NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator();
        $validator->initialize($context);
        $demoChoice = new DemoArrayChoice();

        $validator->validate($demoChoice, new \NS\UtilBundle\Validator\Constraints\ArrayChoice());
    }
}