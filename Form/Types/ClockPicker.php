<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClockPicker extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        if($options['widget'] == 'text')
        {
            $hourOptions         = $builder->get('hour')->getOptions();
            $minOptions          = $builder->get('minute')->getOptions();
           
            $hourOptions['attr'] = array_merge($minOptions['attr'],array('size'=> 1, 'maxlength'=> 2 ,'class'=> 'gsClockPicker','data-clockField'=>'clockHour', 'data-clockGroup'=> 'start_time'));
            $minOptions['attr']  = array_merge($minOptions['attr'],array('size'=> 1, 'maxlength'=> 2 ,'class'=> 'gsClockPicker','data-clockField'=>'clockMinutes', 'data-clockGroup'=> 'start_time'));
            
            $builder->remove('hour')
                    ->add('hour',null,$hourOptions)
                    ->remove('minute')
                    ->add('minute',null,$minOptions);
        }
        
        $attr = array('class'=>'gsClockPicker', 'data-clockGroup'=>'start_time', 'data-clockField'=>'clockMeridian');
        $builder->add('meridian','checkbox',array('label'=>'AM', 'attr'=>$attr, 'label_attr'=>array('class'=>'meridian')));
    }

    public function getName()
    {
        return 'clockpicker';
    }
    
    public function getParent()
    {
        return 'time';
    }
}
