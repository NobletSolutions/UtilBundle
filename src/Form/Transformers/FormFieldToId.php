<?php

namespace NS\UtilBundle\Form\Transformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;

class FormFieldToId implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityMgr;

    private $obj;

    public function __construct(EntityManagerInterface $entityMgr)
    {
        $this->entityMgr = $entityMgr;
    }

    /**
     *
     * @param object $object
     */
    public function setObject($object): void
    {
        $this->obj = $object;
    }

    /**
     *
     * @param integer $id
     * @return string
     */
    public function transform($id): string
    {
        if (null === $id) {
            return '';
        }

        // This should probably test for an interface
        // We should also make what method we are looking for configurable
        if (is_object($this->obj) && method_exists($this->obj, 'getType')) {
            $entity = $this->entityMgr->getRepository($this->obj->getType()->getClassMatch())->find($id);
        } else {
            $entity = $this->obj;
        }

        return json_encode(array($id => $entity . ''));
    }

    /**
     *
     * @param mixed $ids
     * @return int
     * @throws UnexpectedTypeException
     * @throws \Exception
     */
    public function reverseTransform($ids)
    {
        if ('' === $ids || null === $ids) {
            return null;
        }

        if (!is_string($ids)) {
            throw new UnexpectedTypeException($ids, 'string');
        }

        $idsArray = json_decode($ids, true);

        if (empty($idsArray)) {
            return null;
        }

        if (count($idsArray) > 1) {
            throw new \Exception('Too many ids');
        }

        return key($idsArray);
    }
}
