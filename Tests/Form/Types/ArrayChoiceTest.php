<?php

namespace NS\UtilBundle\Tests\Form\Types;

use NS\UtilBundle\Tests\Form\Fixtures\SubArrayChoice;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpKernel\Kernel;

class ArrayChoiceTest extends TypeTestCase
{
    private $values = [
        SubArrayChoice::CHOICE_ONE => 'One',
        SubArrayChoice::CHOICE_TWO => 'Two',
        SubArrayChoice::CHOICE_THREE => 'Three',
        SubArrayChoice::CHOICE_FOUR => 'Four',
    ];

    /**
     * @param $current
     * @param $value
     * @param $expected
     *
     * @dataProvider getChoices
     */
    public function testEqual($current, $value, $expected)
    {
        $choice = new SubArrayChoice($current);
        $this->assertEquals($expected, $choice->equal($value));
    }

    public function getChoices()
    {
        return [
            [null, SubArrayChoice::CHOICE_ONE, false],
            [SubArrayChoice::CHOICE_ONE, SubArrayChoice::CHOICE_ONE, true],
            [SubArrayChoice::CHOICE_ONE, '1', true],
            [SubArrayChoice::CHOICE_ONE, 'One', true],
            [SubArrayChoice::CHOICE_ONE, 'Two', false],
            [SubArrayChoice::CHOICE_ONE, '2', false],
            [SubArrayChoice::CHOICE_ONE, 2, false],
        ];
    }

    public function testConstructor()
    {
        $form     = $this->factory->create(SubArrayChoice::class);
        $choices  = $form->getConfig()->getOption('choices');
        $expected = array_flip($this->values);

        $this->assertCount(4, $choices);
        $this->assertEquals($expected, $choices);
    }

    public function testExcluded()
    {
        $form = $this->factory->create(SubArrayChoice::class, null, ['exclude_choices' => [SubArrayChoice::CHOICE_FOUR]]);
        $choices = $form->getConfig()->getOption('choices');

        unset($this->values[SubArrayChoice::CHOICE_FOUR]);
        $expected = array_flip($this->values);

        $this->assertCount(3, $choices);
        $this->assertEquals($expected, $choices);
    }

    protected function getExtensions()
    {
        return [new PreloadedExtension([new SubArrayChoice()], [])];
    }
}
