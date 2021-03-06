<?php

namespace NS\UtilBundle\Form\Transformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * ChoiceTransformer constructor.
     * @param $class
     */
    public function __construct($class)
    {
        $this->className = $class;
    }

    /**
     * Transforms an object (ArrayChoice) to a string (number).
     *
     * @param  ArrayChoice|null $object
     * @return integer
     */
    public function transform($object)
    {
        if (null === $object || !is_object($object)) {
            return "";
        }

        return $object->getValue();
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
        if (!empty($number) && !is_numeric($number)) {
            throw new TransformationFailedException('Unable to transform non numeric types');
        }

        try {
            $obj = new $this->className($number);
        } catch (\UnexpectedValueException $e) {
            throw new TransformationFailedException($e->getMessage());
        }

        return $obj;
    }
}
