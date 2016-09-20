<?php

namespace NS\UtilBundle\Validator\Constraints;

use \Symfony\Component\Validator\Constraint;
use \Symfony\Component\Validator\ConstraintValidator;
use \NS\UtilBundle\Form\Types\ArrayChoice;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->multipleMessage)
                    ->addViolation();
            }
        }
    }

    /**
     *
     * @param \NS\UtilBundle\Form\Types\ArrayChoice $value
     * @return boolean
     */
    public function isValid($value)
    {
        return ($value instanceof ArrayChoice && $value->getValue() != ArrayChoice::NO_SELECTION);
    }
}
