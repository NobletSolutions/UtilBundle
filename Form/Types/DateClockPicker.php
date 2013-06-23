<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateClockPicker extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options)
    {
        $dateOptions = $builder->get('date')->getOptions();
        $timeOptions = $builder->get('time')->getOptions();
        
        $builder->remove('date')
                ->add('date', 'datepicker', $dateOptions)
                ->remove('time')
                ->add('time','clockpicker',$timeOptions);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'time_widget' => 'text'
        ));
    }

    public function getName()
    {
        return 'dateclockpicker';
    }
    
    public function getParent()
    {
        return 'datetime';
    }
}