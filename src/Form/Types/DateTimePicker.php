<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;

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
            $timeOptions = array_merge($timeOptions,array('hours'=>$options['preferred_choices']['hours'],'minutes'=>$options['preferred_choices']['minutes']));
            $builder->remove('time')
                    ->add('time','time',$timeOptions);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions( OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'placeholder'=>array('hour'=>'HR','minute'=>'MIN')
        ));

        $resolver->setDefined(array(
            'preferred_choices'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datetimepicker';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'datetime';
    }
}
