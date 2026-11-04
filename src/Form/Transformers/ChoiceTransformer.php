<?php

namespace NS\UtilBundle\Form\Transformers;

use NS\UtilBundle\Form\Types\ArrayChoice;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use UnexpectedValueException;

class ChoiceTransformer implements DataTransformerInterface
{
    private string $className;

    public function __construct(string $class)
    {
        $this->className = $class;
    }

    /**
     * Transforms an object (ArrayChoice) to a string (number).
     *
     * @param ArrayChoice|null $value
     */
    public function transform($value): ?string
    {
        if (!is_object($value)) {
            return null;
        }

        return (string)$value->getValue();
    }

    /**
     * Transforms a string (number) to an object (ArrayChoice).
     *
     * @param string $value
     *
     * @return ArrayChoice|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($value): ?ArrayChoice
    {
        if (!empty($value) && !is_numeric($value)) {
            throw new TransformationFailedException('Unable to transform non numeric types');
        }

        try {
            $obj = new $this->className($value === '' ? null : $value);
        } catch (UnexpectedValueException $e) {
            throw new TransformationFailedException($e->getMessage());
        }

        return $obj;
    }
}
