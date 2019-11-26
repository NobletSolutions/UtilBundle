<?php

namespace NS\UtilBundle\Tests\Validator;

use NS\UtilBundle\Form\Types\ArrayChoice;

class DemoArrayChoice extends ArrayChoice
{
    public const FIRST = 1;

    protected $values = [
        self::FIRST => 'First'
    ];

    public function getName(): string
    {
        return 'DemoArrayChoice';
    }     
}
