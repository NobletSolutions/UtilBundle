<?php

namespace NS\UtilBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use NS\UtilBundle\Form\Types\ArrayChoice;
/**
 * Description of ArrayChoiceValidator
 *
 * @author gnat
 */
class ArrayChoiceValidator extends ConstraintValidator
{
    /**
     * @param ArrayChoice $value
     * @param Constraint  $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$this->isValid($value)) {
            // If you're using the new 2.5 validation API (you probably are!)
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    /**
     *
     * @param ArrayChoice $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        return ($value instanceof ArrayChoice && $value->getValue() != ArrayChoice::NO_SELECTION);
    }
}
