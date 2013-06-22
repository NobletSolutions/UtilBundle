<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\AbstractType;

class ClockPicker extends AbstractType
{
    public function getName()
    {
        return 'clockpicker';
    }
    
    public function getParent()
    {
        return 'time';
    }
}
