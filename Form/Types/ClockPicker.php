<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClockPicker extends AbstractType
{
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        parent::buildForm($builder, $options);
//        $builder->add('meridian','checkbox',array('label'=>'AM','attr'=>array('class'=>"gsClockPicker", 'data-clockGroup'=>"start_time", 'data-clockField'=>"clockMeridian")));
//    }

    public function getName()
    {
        return 'clockpicker';
    }
    
    public function getParent()
    {
        return 'time';
    }
}
