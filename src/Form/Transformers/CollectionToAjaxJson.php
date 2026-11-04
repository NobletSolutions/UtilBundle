<?php

namespace NS\UtilBundle\Form\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JsonException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class CollectionToAjaxJson implements DataTransformerInterface
{
    private EntityManagerInterface $entityMgr;

    private string $className;

    /**
     * CollectionToAjaxJson constructor.
     *
     * @param EntityManagerInterface $em
     * @param                        $class
     */
    public function __construct(EntityManagerInterface $em, string $class)
    {
        $this->entityMgr = $em;
        $this->className = $class;
    }

    /**
     * @param mixed $entities
     *
     * @return null|string
     */
    public function transform(mixed $entities): string|false|null
    {
        if (empty($entities)) {
            return "";
        }

        if (!$entities instanceof Collection) {
            throw new UnexpectedTypeException($entities, Collection::class);
        }

        $idsArray = [];
        // check for interface...
        foreach ($entities as $entity) {
            $idsArray[$entity->getId()] = $entity->getAjaxDisplay();
        }

        if (empty($idsArray)) {
            return null;
        }

        return json_encode($idsArray);
    }

    /**
     * @param mixed $value
     *
     * @return ArrayCollection|null
     */
    public function reverseTransform(mixed $value): mixed
    {
        if ('' === $value || null === $value) {
            return new ArrayCollection();
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

        return $this->entityMgr->getRepository($this->className)->getByIds(array_keys($idsArray));
    }
}
