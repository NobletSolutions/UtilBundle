<?php

namespace NS\UtilBundle\Form\Transformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SetChoiceTransformer implements DataTransformerInterface
{
    private $_class;
    
    public function __construct($class)
    {
        $this->_class = $class;
    }

    /**
     * Transforms an object (ArrayChoice) to a string (number).
     *
     * @param  ArrayChoice|null $issue
     * @return integer
     */
    public function transform($object)
    {
        if (null === $object) 
            return "";

        return $object->toArray();
    }

    /**
     * Transforms a string (number) to an object (ArrayChoice).
     *
     * @param  string $number
     * @return ArrayChoice|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($number)
    {
        if (!is_array($number))
            throw new TransformationFailedException('Unable to transform non numeric types');

        try
        {
            $obj = new $this->_class($number);
        }
        catch(\UnexpectedValueException $e)
        {
            throw new TransformationFailedException($e->getMessage());
        }
        
        return $obj;
    }
}