<?php

namespace NS\UtilBundle\Form\Transformers;

use JsonException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;

class FormFieldToId implements DataTransformerInterface
{
    private EntityManagerInterface $entityMgr;

    private ?object $obj = null;

    public function __construct(EntityManagerInterface $entityMgr)
    {
        $this->entityMgr = $entityMgr;
    }

    public function setObject(?object $object): void
    {
        $this->obj = $object;
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        // This should probably test for an interface
        // We should also make what method we are looking for configurable
        if (is_object($this->obj) && method_exists($this->obj, 'getType')) {
            $entity = $this->entityMgr->getRepository($this->obj->getType()->getClassMatch())->find($value);
        } else {
            $entity = $this->obj;
        }

        try {
            return json_encode([$value => (string)$entity], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TransformationFailedException('Invalid autocomplete JSON.', 0, $e);
        }
    }

    /**
     * @param ?string $value The ids
     * @throws UnexpectedTypeException
     */
    public function reverseTransform(mixed $value): ?int
    {
        if ('' === $value || null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            $idsArray = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TransformationFailedException('Invalid autocomplete JSON.', 0, $e);
        }

        if (!is_array($idsArray)) {
            throw new TransformationFailedException('Autocomplete data must be a JSON object or array.');
        }

        if (empty($idsArray)) {
            return null;
        }

        if (count($idsArray) > 1) {
            throw new TransformationFailedException('Too many ids');
        }

        return key($idsArray);
    }
}
