<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class ArrayChoice extends ChoiceType implements \Serializable, \Iterator
{
    protected $values = array();

    protected $current; 

    protected $position;
    
    public function __construct($value = null)
    {
        $this->current = ($value == null || !isset($this->values[$value])) ? 0 : $value;
        $this->position = $this->current;
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

    // Form AbstractType functions
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => __CLASS__,
            'choices'    => $this->values,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }
    
    // Serializer Implementation
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->current,
            $this->values,
            $this->position,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->current,
            $this->values,
            $this->position,
        ) = unserialize($serialized);
    }
    
    // Iterator Implementations
    public function current()
    {
        return $this->values[$this->position];
    }
    
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        return ++$this->position;
    }
    
    public function valid()
    {
        return isset($this->values[$this->position]);
    }
    
    public function rewind()
    {
        reset($this->values);

        $this->position = key($this->values);
    }
}
