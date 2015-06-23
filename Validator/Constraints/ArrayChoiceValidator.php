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
    /**
     * @param \NS\UtilBundle\Form\Types\ArrayChoice $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$this->isValid($value)) {
            // If you're using the new 2.5 validation API (you probably are!)
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    /**
     *
     * @param \NS\UtilBundle\Form\Types\ArrayChoice $value
     * @return boolean
     */
    public function isValid($value)
    {
        return ($value instanceof \NS\UtilBundle\Form\Types\ArrayChoice && $value->getValue() != \NS\UtilBundle\Form\Types\ArrayChoice::NO_SELECTION);
    }
}
