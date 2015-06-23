<?php

namespace NS\UtilBundle\Tests\Validator;

class DemoArrayChoice extends \NS\UtilBundle\Form\Types\ArrayChoice
{
    const FIRST = 1;

    protected $values = array(self::FIRST=>'First');

    public function getName()
    {
        return 'DemoArrayChoice';
    }     
}
