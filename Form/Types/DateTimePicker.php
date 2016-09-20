<?php

namespace NS\UtilBundle\Form\Types;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions( OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'empty_value'=>array('hour'=>'HR','minute'=>'MIN')
        ));

        if(method_exists($resolver,'setDefined')) {
            $resolver->setDefined(array('preferred_choices'));
        } else {
            $resolver->setOptional(array('preferred_choices'));
        }
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
