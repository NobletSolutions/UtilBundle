<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClockPicker extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $group = '_' . bin2hex(random_bytes(8));

        foreach (['hour' => ['clockHours', 1], 'minute' => ['clockMinutes', 5]] as $field => [$clockField, $increment]) {
            if (!$builder->has($field)) {
                continue;
            }

            $childOptions         = $builder->get($field)->getOptions();
            $childOptions['attr'] = array_merge($childOptions['attr'], [
                'size'            => 1,
                'maxlength'       => 2,
                'class'           => 'gsClockPicker',
                'data-clockField' => $clockField,
                'data-clockGroup' => $group,
                'data-increment'  => $increment,
            ]);
            $builder->add($field, TextType::class, $childOptions);
        }

        // The clock JavaScript converts the displayed hours to 24-hour time on submit.
        $builder->add('meridian', CheckboxType::class, [
            'mapped'     => false,
            'label'      => 'AM',
            'attr'       => ['class' => 'gsClockPicker', 'data-clockGroup' => $group, 'data-clockField' => 'clockMeridian'],
            'required'   => false,
            'label_attr' => ['class' => 'meridian'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['widget' => 'text', 'html5' => false]);
        $resolver->setAllowedValues('widget', ['text']);
    }

    public function getParent(): string
    {
        return TimeType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'clockpicker';
    }
}
