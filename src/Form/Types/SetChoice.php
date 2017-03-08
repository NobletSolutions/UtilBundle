<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use NS\UtilBundle\Form\Transformers\SetChoiceTransformer;

/**
 * Description of SetChoice
 *
 * @author gnat
 */
abstract class SetChoice extends AbstractType
{
    /**
     * exception code when input values are not array or string
     */
    const IAE_NOT_ARRAY_OR_STRING = 1;

    /**
     * exception code when input values are not any of supported types
     */
    const IAE_UNSUPPORTED_TYPE = 2;

    /**
     * exception code when input value contains duplicate values
     */
    const IAE_DUPLICATES = 3;

    /**
     * exception code when input contains values that are not in initial set
     */
    const IAE_NO_VALUE_IN_SET = 4;

    /**
     * exception code when set is larger than MAX_VALUES_IN_SET elements
     */
    const IAE_SET_TO_LONG = 5;

    /**
     * maximum number of elements in set, as we are running mostly on 32bit platforms this is set to 32
     */
    const MAX_VALUES_IN_SET = 64;

    /**
     * possible values in this set
     * @var array
     */
    protected $set = array();

    /**
     * the actual values in this set
     * @var array
     */
    protected $setValues = array();

    /**
     * @var array
     */
    protected $groupedSet = array();

    /**
     *
     * Set dataype
     *
     * @param mixed $possibleValues array|string with comma separated values for this set
     * @param mixed $assignValues null|array|App_Set|integer|string with comma separated values
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function __construct($assignValues = null)
    {
        if (empty($this->groupedSet) && empty($this->set)) {
            throw new \UnexpectedValueException("protected variables set and groupedSet are both empty");
        }

        if (!empty($this->groupedSet)) {
            $this->set = $this->flattenGroups();
        }

        if (count($this->set) > self::MAX_VALUES_IN_SET) {
            throw new \InvalidArgumentException(sprintf('%d is the maximum number of values you can have in a set.', self::MAX_VALUES_IN_SET), self::IAE_SET_TO_LONG);
        }

        if (count($assignValues) > self::MAX_VALUES_IN_SET) {
            throw new \InvalidArgumentException(sprintf('%d is the maximum number of values you can have in a set.', self::MAX_VALUES_IN_SET), self::IAE_SET_TO_LONG);
        }

        //prepare setValues as assoc array
        $this->setValues = array_fill_keys($this->set, 0);

        if (!is_null($assignValues)) {
            $this->assign($assignValues);
        }
    }

    /**
     * @return array
     */
    public function flattenGroups()
    {
        $set = array();

        foreach ($this->groupedSet as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v)
                    $set[] = $v;
            } else
                $set[] = $value;
        }

        return $set;
    }

    /**
     * Returns set values as string
     *
     * @return string
     */
    public function __toString()
    {
        $values = $this->toArray();

        return implode(',', $values);
    }

    /**
     * asign values to set
     *
     * @param mixed $values array|App_Set|integer|string with comma separated values
     *
     * @throws \InvalidArgumentException
     *
     */
    public function assign($values)
    {
        $this->setUnset($this->set, false);
        $this->setValue($values);
    }

    /**
     * set value in set
     *
     * @param @param mixed $values array|App_Set|integer|string with comma separated values to add to this set
     *
     * @throws \InvalidArgumentException
     */
    public function setValue($values)
    {
        $values = $this->prepareValues($values, true);

        $this->setUnset($values, true);
    }

    /**
     * unset value in set
     *
     * @param @param mixed $values array|App_Set|integer|string with comma separated values to remove from this set
     *
     * @throws \InvalidArgumentException
     */
    public function unsetValue($values)
    {
        $values = $this->prepareValues($values, true);

        $this->setUnset($values, false);
    }

    /**
     * is value in set?
     *
     * @param string $value
     *
     * @return bool
     */
    public function inSet($value)
    {
        //true returned when key exists and is set to 1
        if (array_key_exists($value, $this->setValues)) {
            return ($this->setValues[$value] == 1);
        }

        return false;
    }

    /**
     * Returns set values as array
     *
     * @return array
     */
    public function toArray()
    {
        $ret = array();

        foreach ($this->setValues as $key => $value) {
            if (0 != $value) {
                $ret[] = $key;
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getValueArray()
    {
        $ret = array();

        if (empty($this->groupedSet)) {
            foreach ($this->set as $value) {
                $ret[$value] = $value;
            }
        } else {
            foreach ($this->groupedSet as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $ret[$key][$v] = $v;
                    }
                } else {
                    $ret[$value] = $value;
                }
            }
        }

        return $ret;
    }

    /**
     * Returns set values as integer
     *
     * @return integer
     */
    public function toInteger()
    {
        $bitString = '';
        foreach ($this->setValues as $value) {
            $bitString = $value . $bitString;
        }

        return bindec($bitString);
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->toInteger();
    }

    /**
     * Prepares input so we can set the values
     *
     * @param mixed $values array|string comma separated string of possible values
     * @param bool $allPossible , try to convert from all possible types
     * @throws \InvalidArgumentException
     */
    private function prepareValues($values, $allPossible)
    {
        if ($allPossible) {
            if (is_numeric($values)) {
                $values = $this->fromInteger((integer)$values);
            } elseif ($values instanceof self) {
                $values = (string)$values;
            } elseif (is_string($values)) {
                $values = explode(',', $values);
            } elseif (is_null($values)) {
                $values = array();
            } elseif (!is_array($values)) {
                throw new \InvalidArgumentException(sprintf('Parameter should be any of the following type array|%s|integer|string.', __CLASS__), self::IAE_UNSUPPORTED_TYPE);
            }
        } else {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else if (!is_array($values)) {
                throw new \InvalidArgumentException('Values parameter should be either array or string.', self::IAE_ARRAY_OR_STRING);
            }
        }

        //trim whitespace
        $ar = array();
        foreach ($values as $pv) {
            array_push($ar, trim($pv));
        }

        $values = $ar;

        //no duplicates allowed
        if (count(array_unique($values)) != count($values)) {
            throw new \InvalidArgumentException('Values parameter contains Duplicate values.', self::IAE_DUPLICATES);
        }

        //32 items is the limit, as we are optimizing this for 32bit platform
        if (count($values) > self::MAX_VALUES_IN_SET) {
            throw new \InvalidArgumentException(sprintf('%d is the maximum number of values you can have in a set.', self::MAX_VALUES_IN_SET), self::IAE_SET_TO_LONG);
        }

        return $values;
    }

    /**
     *
     * sets/unsets the values
     * @param array $values array of values to set/unset
     * @param bool $set true sets them, false unsets
     *
     * @throws \InvalidArgumentException
     */
    private function setUnset($values, $set)
    {
        $val = $set ? 1 : 0;

        foreach ($values as $value) {
            if (array_key_exists($value, $this->setValues)) {
                $this->setValues[$value] = $val;
            } else {
                throw new \InvalidArgumentException(sprintf('\'%s\' is not valid value for this set. Valid values are: %s.', $value, implode(',', $this->set)), self::IAE_NO_VALUE_IN_SET);
            }
        }
    }

    /**
     * Tries to convert integer value to representation of this set
     *
     * @param integer $values
     *
     * @return array
     */
    private function fromInteger($values)
    {
        $values = (integer)$values;
        $possible = $this->set;
        $ret = array();

        while ($key = array_shift($possible)) {
            if (1 == ($values & 0x01)) {
                $ret[] = $key;
            }

            $values = $values >> 1;
        }

        return $ret;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new SetChoiceTransformer(get_called_class());
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array_flip($this->getValueArray()),
            'multiple' => true,
            'placeholder' => 'Please Select...',
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
