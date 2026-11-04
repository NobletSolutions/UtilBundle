<?php

namespace NS\UtilBundle\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Description of ArrayChoice
 *
 * @author gnat
 *
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ArrayChoiceConstraint extends Constraint
{
    public string $message = 'No option was selected';

    public function validatedBy(): string
    {
        return 'NS\UtilBundle\Validator\Constraints\ArrayChoiceValidator';
    }
}
