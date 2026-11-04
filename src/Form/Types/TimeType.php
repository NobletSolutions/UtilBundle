<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType as SymfonyTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['widget'] !== 'choice') {
            return;
        }

        foreach (['hour' => 'hours', 'minute' => 'minutes', 'second' => 'seconds'] as $field => $part) {
            if (!$builder->has($field)) {
                continue;
            }

            $child = $builder->get($field);
            $childOptions = $child->getOptions();
            $childOptions['preferred_choices'] = $options['preferred_choices'][$part];
            $builder->add($field, $child->getType()->getInnerType()::class, $childOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('widget', 'choice');
        $resolver->setDefault('preferred_choices', []);
        $resolver->setNormalizer('preferred_choices', static function (Options $options, $choices): array {
            if (!is_array($choices)) {
                return ['hours' => $choices, 'minutes' => $choices, 'seconds' => $choices];
            }

            return array_replace(['hours' => [], 'minutes' => [], 'seconds' => []], $choices);
        });
    }

    public function getParent(): string
    {
        return SymfonyTimeType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ns_time';
    }
}
