<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePicker extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options): void
    {
        if ($options['widget'] === 'single_text') {
            return;
        }

        $dateOptions = $builder->get('date')->getOptions();
        $timeOptions = $builder->get('time')->getOptions();

        $builder->remove('date')
                ->add('date', DatePickerType::class, $dateOptions);

        if (isset($options['preferred_choices'])) {
            $timeOptions = array_merge($timeOptions, ['hours' =>$options['preferred_choices']['hours'], 'minutes' =>$options['preferred_choices']['minutes']]);
            $builder->remove('time')
                    ->add('time', TimeType::class,$timeOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions( OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_widget' => 'single_text',
            'placeholder'=> ['hour' =>'HR', 'minute' =>'MIN']
        ]);

        $resolver->setDefined([
            'preferred_choices'
        ]);
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
