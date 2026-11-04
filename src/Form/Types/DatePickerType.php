<?php

namespace NS\UtilBundle\Form\Types;

use IntlDateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * A Date picker text type
 *
 * @author gnat
 */
class DatePickerType extends AbstractType
{
    public const DEFAULT_FORMAT = IntlDateFormatter::MEDIUM;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget'   => 'single_text',
            'compound' => false,
            'html5' => false,
        ]);
    }

    public function finishView( FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['type'] = 'text';
    }

    public function getParent(): string
    {
        return DateType::class;
    }
    public function getBlockPrefix(): string
    {
        return 'datepicker';
    }

}
