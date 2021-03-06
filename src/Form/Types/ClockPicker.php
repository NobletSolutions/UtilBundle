<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClockPicker extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['widget'] == 'text') {
            $group               = '_'.md5(microtime().rand(500, 500000));
            $hourOptions         = $builder->get('hour')->getOptions();
            $minOptions          = $builder->get('minute')->getOptions();

            $hourOptions['attr'] = array_merge($minOptions['attr'],array('size'=> 1, 'maxlength'=> 2 ,'class'=> 'gsClockPicker','data-clockField'=>'clockHours', 'data-clockGroup'=> $group, 'data-increment'=>1));
            $minOptions['attr']  = array_merge($minOptions['attr'],array('size'=> 1, 'maxlength'=> 2 ,'class'=> 'gsClockPicker','data-clockField'=>'clockMinutes', 'data-clockGroup'=> $group, 'data-increment'=>5));

            $builder->remove('hour')
                    ->add('hour',null,$hourOptions)
                    ->remove('minute')
                    ->add('minute',null,$minOptions);
        }

        $attr = array('class'=>'gsClockPicker', 'data-clockGroup'=>$group, 'data-clockField'=>'clockMeridian');
        $builder->add('meridian','checkbox',array('label'=>'AM', 'attr'=>$attr, 'required'=>false,'label_attr'=>array('class'=>'meridian','required'=>false)));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'clockpicker';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'time';
    }
}
