<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * A Date picker text type
 *
 * @author gnat
 */
class DatePickerType extends AbstractType
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
    
    public function getParent()
    {
        return 'date';
    }
}
