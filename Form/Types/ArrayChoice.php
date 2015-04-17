<?php

namespace NS\UtilBundle\Form\Types;

use \NS\UtilBundle\Form\Transformers\ChoiceTransformer;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\FormInterface;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class ArrayChoice extends AbstractType implements \Iterator
{
    const NO_SELECTION = -1;

    protected $values  = array(self::NO_SELECTION => 'N/A');
    protected $current = self::NO_SELECTION;

    public function __construct($value = null)
    {
        if (!is_null($value)) {
            if (is_numeric($value)) {
                if (!isset($this->values[$value]) && $value != self::NO_SELECTION) {
                    throw new \UnexpectedValueException('Invalid numeric choice value: ' . $value . ' for ' . get_called_class());
                }

                $this->current = $value;
            }
            else if (is_string($value)) {
                foreach ($this->values as $key => $v) {
                    if (strcasecmp($v, $value) == 0) {
                        $this->current = $key;
                        return $this;
                    }
                }

                throw new \UnexpectedValueException('Invalid string choice value: ' . $value);
            }
        }
    }

    public function __toString()
    {
        return (isset($this->values[$this->current]) ? $this->values[$this->current] : "");
    }

    public function setValue($value)
    {
        $this->current = ($value == null || !isset($this->values[$value])) ? 0 : $value;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getValue()
    {
        return $this->current;
    }

    public function getLabels()
    {
        return $this->values;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ChoiceTransformer(get_called_class());
        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['special_values'])) {
            $view->vars['special_values'] = $options['special_values'];
        }
    }

    // Form AbstractType functions
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices'     => $this->values,
            'empty_value' => 'Please Select...',
        ));

        $resolver->setOptional(array('special_values'));

        $resolver->addAllowedTypes(array('special_values' => 'array'));
    }

    public function isValid()
    {
        return ($this->current != self::NO_SELECTION && isset($this->values[$this->current]));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function equal($var)
    {
        if (is_integer($var)) {
            return ($this->current == $var);
        }

        if (is_string($var)) {
            return ($this->values[$this->current] == $var);
        }

        return false;
    }

    public function current()
    {
        return current($this->values);
    }

    public function next()
    {
        next($this->values);
    }

    public function key()
    {
        return key($this->values);
    }

    public function valid()
    {
        return isset($this->values[$this->key()]);
    }

    public function rewind()
    {
        reset($this->values);
    }

    public function reverse()
    {
        $this->values = array_reverse($this->values);
        $this->rewind();
    }
}
