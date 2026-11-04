<?php

namespace NS\UtilBundle\Form\Transformers;

use InvalidArgumentException;
use NS\UtilBundle\Form\Types\ArrayChoice;
use NS\UtilBundle\Form\Types\SetChoice;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use UnexpectedValueException;

class SetChoiceTransformer implements DataTransformerInterface
{
    private string $className;

    public function __construct(string $class)
    {
        $this->className = $class;
    }

    /**
     * Transforms an object (ArrayChoice) to a string (number).
     *
     * @param mixed $value
     * @return int
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        } elseif(!$value instanceof SetChoice) {
            throw new InvalidArgumentException(sprintf('Argument is expected to be of type SetChoice got: %s',get_debug_type($value)));
        }

        return $value->toArray();
    }

    /**
     * Transforms a string (number) to an object (ArrayChoice).
     *
     * @param  string $value
     * @return ArrayChoice|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($value)
    {
        $value ??= [];

        if (!is_array($value)) {
            throw new TransformationFailedException('Unable to transform non numeric types');
        }

        try {
            $obj = new $this->className($value);
        } catch (UnexpectedValueException $e) {
            throw new TransformationFailedException($e->getMessage());
        }

        return $obj;
    }
}
