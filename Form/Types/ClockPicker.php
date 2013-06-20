<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ClockPicker extends DateTimeType
{
    public function getName()
    {
        return 'nsclockpicker';
    }
}
