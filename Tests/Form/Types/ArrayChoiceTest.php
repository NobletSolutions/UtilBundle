<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 13/02/17
 * Time: 12:44 PM
 */

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

    public function testFlip()
    {
        if (Kernel::MAJOR_VERSION == 2) {
            $this->assertEquals($this->values[SubArrayChoice::CHOICE_FOUR], 'Four');
        } else {
            $vars = array_flip($this->values);
            $this->assertArrayHasKey('Four',$vars);
            $this->assertEquals($vars['Four'],4);
        }
    }

    public function testConstructor()
    {
        $form = $this->factory->create(SubArrayChoice::class);
        $choices = $form->getConfig()->getOption('choices');
        $expected = (Kernel::MAJOR_VERSION == 2) ?  $this->values : array_flip($this->values);

        $this->assertCount(4, $choices);
        $this->assertEquals($expected, $choices);
    }

    public function testExcluded()
    {
        $form = $this->factory->create(SubArrayChoice::class, null, ['exclude_choices' => [SubArrayChoice::CHOICE_FOUR]]);
        $choices = $form->getConfig()->getOption('choices');

        unset($this->values[SubArrayChoice::CHOICE_FOUR]);
        $expected = (Kernel::MAJOR_VERSION == 2) ?  $this->values : array_flip($this->values);

        $this->assertCount(3, $choices);
        $this->assertEquals($expected, $choices);
    }

    protected function getExtensions()
    {
        return [new PreloadedExtension([new SubArrayChoice()], [])];
    }
}
