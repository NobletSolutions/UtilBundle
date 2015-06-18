<?php

namespace NS\UtilBundle\Validator\Constraints;

use \NS\UtilBundle\Form\Types\ArrayChoice;
use \Symfony\Component\Validator\Constraint;
use \Symfony\Component\Validator\ConstraintValidator;

/**
 * Description of ArrayChoiceValidator
 *
 * @author gnat
 */
class ArrayChoiceValidator extends ConstraintValidator
{
    /**
     * @param ArrayChoice $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->isValid($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    /**
     * @param ArrayChoice $value
     * @return boolean
     */
    public function isValid($value)
    {
        return ($value instanceof ArrayChoice && $value->getValue() != ArrayChoice::NO_SELECTION);
    }
}