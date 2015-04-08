<?php

namespace NS\UtilBundle\Validator\Constraints;

use \Symfony\Component\Validator\Constraint;
use \Symfony\Component\Validator\ConstraintValidator;

/**
 * Description of ArrayChoiceValidator
 *
 * @author gnat
 */
class ArrayChoiceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \NS\UtilBundle\Form\Types\ArrayChoice || $value->getValue() == \NS\UtilBundle\Form\Types\ArrayChoice::NO_SELECTION) {
            // If you're using the new 2.5 validation API (you probably are!)
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('%string%', $value)
//                ->addViolation();

            // If you're using the old 2.4 validation API
            $this->context->addViolation($constraint->message);
        }
    }
}