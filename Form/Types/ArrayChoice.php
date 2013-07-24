<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class ArrayChoice extends AbstractType
{
    const NO_SELECTION = -1;
    
    protected $values  = array(self::NO_SELECTION => 'N/A');

    protected $current = self::NO_SELECTION; 

    public function __construct($value = null)
    {
        if(!is_null($value))
        {
            if(is_numeric($value))
            {
                if(!isset($this->values[$value]) && $value != self::NO_SELECTION)
                    throw new \UnexpectedValueException(__LINE__.' Invalid choice value: '.$value.' for '.  get_called_class());
                
                $this->current = $value;
            }
            else if(is_string($value))
            {
                foreach($this->values as $key => $v)
                {
                    if(strcasecmp($v, $value) == 0)
                    {
                        $this->current = $key;
                        return $this;
                    }
                }
                
                throw new \UnexpectedValueException(__LINE__.' Invalid choice value: '.$value);
            }
        }
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
        return ($this->current != self::NO_SELECTION && isset($this->values[$this->current]));
    }
    
    public function getParent()
    {
        return 'choice';
    }
}
