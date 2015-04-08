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
class ArrayChoice extends Constraint
{
    public $message = 'No option was selected';
}