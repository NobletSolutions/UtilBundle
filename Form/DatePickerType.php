<?php

namespace NS\UtilBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * A Date picker text type
 *
 * @author gnat
 */
class DatePickerType extends DateType
{
    const DEFAULT_FORMAT = \IntlDateFormatter::MEDIUM;

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'widget'    => 'single_text',
            'compound'  => false,
        ));
    }

    public function finishView( FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->vars['type'] = 'text';
    }

    public function getName()
    {
        return 'datepicker';
    }
}
