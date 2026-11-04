<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateClockPicker extends AbstractType
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
                ->add('date', DatePickerType::class, $dateOptions)
                ->remove('time')
                ->add('time', ClockPicker::class,$timeOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_widget' => 'single_text',
            'time_widget' => 'text'
        ]);
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
