<?php

namespace NS\UtilBundle\Tests\Validator;

use NS\UtilBundle\Form\Types\ArrayChoice;

class DemoArrayChoice extends ArrayChoice
{
    const FIRST = 1;

    protected $values = [
        self::FIRST => 'First'
    ];

    public function getName()
    {
        return 'DemoArrayChoice';
    }     
}
