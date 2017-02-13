<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 13/02/17
 * Time: 12:46 PM
 */

namespace NS\UtilBundle\Tests\Form\Fixtures;

use NS\UtilBundle\Form\Types\ArrayChoice;

class SubArrayChoice extends ArrayChoice
{
    const CHOICE_ONE = 1;
    const CHOICE_TWO = 2;
    const CHOICE_THREE = 3;
    const CHOICE_FOUR = 4;

    protected $values = [
        self::CHOICE_ONE => 'One',
        self::CHOICE_TWO => 'Two',
        self::CHOICE_THREE => 'Three',
        self::CHOICE_FOUR => 'Four',
        ];

    public function getName()
    {
        return 'sub_array_choice';
    }
}
