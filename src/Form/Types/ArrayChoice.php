<?php

namespace NS\UtilBundle\Form\Types;

use NS\UtilBundle\Form\Transformers\ChoiceTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ArrayChoice extends AbstractType implements \Iterator
{
    const OUT_OF_RANGE = -9999;
    const NO_SELECTION = -1;

    protected $groupedValues = null;
    protected $values = [self::NO_SELECTION => 'N/A'];
    protected $current = self::NO_SELECTION;

    /**
     * @param null $value
     */
    public function __construct($value = null)
    {
        if ($value !== null) {
            if (is_numeric($value)) {
                if (!isset($this->values[$value]) && $value != self::NO_SELECTION && $value != self::OUT_OF_RANGE) {
                    throw new \UnexpectedValueException('Invalid numeric choice value: ' . $value . ' for ' . get_called_class());
                }

                $this->current = $value;
            } elseif (is_string($value)) {
                foreach ($this->values as $key => $v) {
                    if (strcasecmp($v, $value) == 0) {
                        $this->current = $key;
                        return;
                    }
                }

                throw new \UnexpectedValueException('Invalid string choice value: ' . $value);
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return isset($this->values[$this->current]) ? $this->values[$this->current] : '';
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->current = ($value === null || !isset($this->values[$value])) ? 0 : $value;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->current;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->values;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ChoiceTransformer(get_called_class());
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['special_values'])) {
            $view->vars['special_values'] = $options['special_values'];
        }
    }

    // Form AbstractType functions

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $values        = $this->values;
        $groupedValues = $this->groupedValues;

        $resolver->setDefaults([
            'placeholder' => 'Please Select...',
            'exclude_choices' => null,
            'choices' => static function (Options $options) use ($values, $groupedValues) {
                if ($groupedValues !== null) {
                    return $groupedValues;
                }

                if (!empty($options['exclude_choices'])) {
                    foreach ($options['exclude_choices'] as $excludedChoice) {
                        if (!isset($values[$excludedChoice])) {
                            throw new InvalidOptionsException(sprintf('%s is not a valid option value to exclude', $excludedChoice));
                        }

                        unset($values[$excludedChoice]);
                    }
                }

                return array_flip($values);
            },
        ]);

        $resolver->setDefined(['special_values']);
        $resolver->setAllowedTypes('special_values', 'array');
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return ($this->current !== self::NO_SELECTION && isset($this->values[$this->current]));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @param $var
     *
     * @return bool
     */
    public function equal($compared)
    {
        if ($compared instanceof ArrayChoice) {
            return ($compared->getValue() == $this->current);
        }

        return ($compared == $this->current || (isset($this->values[$this->current]) && $this->values[$this->current] == $compared));
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     *
     */
    public function next()
    {
        next($this->values);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * @return bool
     */
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
