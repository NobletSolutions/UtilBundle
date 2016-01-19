<?php

namespace NS\UtilBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Description of ArrayChoice
 *
 * @author gnat
 *
 * @Annotation
 */
class ArrayChoiceConstraint extends Constraint
{
    public $message = 'No option was selected';
}
