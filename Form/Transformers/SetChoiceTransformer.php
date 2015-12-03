<?php

namespace NS\UtilBundle\Form\Transformers;

use NS\UtilBundle\Form\Types\ArrayChoice;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SetChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * SetChoiceTransformer constructor.
     * @param $class
     */
    public function __construct($class)
    {
        $this->className = $class;
    }

    /**
     * Transforms an object (ArrayChoice) to a string (number).
     *
     * @param mixed $object
     * @return int
     */
    public function transform($object)
    {
        if (null === $object) {
            return null;
        } elseif(!$object instanceof ArrayChoice) {
            throw new \InvalidArgumentException(sprintf('Argument is expected to be of type ArrayChoice got: %s',get_class($object)));
        }

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
        if (!is_array($number)) {
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
