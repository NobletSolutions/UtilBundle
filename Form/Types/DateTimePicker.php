<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePicker extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options)
    {
        $dateOptions = $builder->get('date')->getOptions();
        $timeOptions = $builder->get('time')->getOptions();
        
        $builder->remove('date')
                ->add('date', 'datepicker', $dateOptions);

        if(isset($options['preferred_choices']))
        {
            $timeOptions = array_merge($timeOptions,array('preferred_choices'=>$options['preferred_choices']));
            $builder->remove('time')
                    ->add('time','time',$timeOptions);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'empty_value'=>array('hour'=>'HR','minute'=>'MIN')
        ));

        $resolver->setOptional(array(
            'preferred_choices'
            ));
    }

    public function getName()
    {
        return 'datetimepicker';
    }
    
    public function getParent()
    {
        return 'datetime';
    }
}
