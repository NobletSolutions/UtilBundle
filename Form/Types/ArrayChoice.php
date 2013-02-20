<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class ArrayChoice extends AbstractType
{
    protected $values = array();

    protected $current; 

    public function __construct($value = null)
    {
        if($value !== null && !isset($this->values[$value]))
            throw new \UnexpectedValueException('Invalid choice value: '.$value);
        
        $this->current = ($value == null) ? -1 : $value;
    }

    public function __toString()
    {
        return (isset($this->values[$this->current]) ? $this->values[$this->current]: "");
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

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $transformer = new \NS\UtilBundle\Form\Transformers\ChoiceTransformer(get_called_class());
        $builder->addModelTransformer($transformer);
    }

    // Form AbstractType functions
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'choices'     => $this->values,
            'empty_value' => 'Please Select...',
        ));
    }

    public function isValid()
    {
        return isset($this->values[$this->current]);
    }

    public function getParent()
    {
        return 'choice';
    }
}
