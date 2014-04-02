<?php

namespace NS\UtilBundle\Form\Extension;

/**
 * Description of ChoiceValue
 *
 * @author gnat
 */
class ChoiceValue
{
    public $value;
    public $context;

    function __construct($value=null, $context=null)
    {
        $this->value   = $value;
        $this->context = $context;
    }
}
