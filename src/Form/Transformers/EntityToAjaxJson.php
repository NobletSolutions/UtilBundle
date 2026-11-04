<?php

namespace NS\UtilBundle\Form\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use NS\UtilBundle\Service\AjaxAutocompleteInterface;

class EntityToAjaxJson implements DataTransformerInterface
{
    private EntityManagerInterface $entityMgr;

    private string $className;

    public function __construct(EntityManagerInterface $em, string $class)
    {
        $this->entityMgr = $em;
        $this->className = $class;
    }

    /**
     * @param mixed $value (the entity)
     *
     * @return string
     */
    public function transform(mixed $value): string
    {
        if (null === $value) {
            return "";
        }

        if (!$value instanceof $this->className) {
            throw new UnexpectedTypeException($value, $this->className);
        }

        if (!$value instanceof AjaxAutocompleteInterface) {
            throw new UnexpectedTypeException($value, 'AjaxAutocompleteInterface');
        }

        $idArray = [$value->getId() => $value->getAjaxDisplay()];

        return json_encode($idArray);
    }

    /**
     * @param mixed $value
     *
     * @return null|object
     * @throws Exception
     */
    public function reverseTransform(mixed $value): ?object
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

        return $this->entityMgr->getRepository($this->className)->find(key($idsArray));
    }
}
