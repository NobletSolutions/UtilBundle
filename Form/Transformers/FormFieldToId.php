<?php

namespace NS\UtilBundle\Form\Transformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of FormFieldToId
 *
 * @author gnat
 */
class FormFieldToId implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $entityMgr;

    /**
     * @var
     */
    private $obj;

    /**
     *
     * @param ObjectManager $entityMgr
     * @return \NS\UtilBundle\Form\Transformers\FormFieldToId
     */
    public function __construct(ObjectManager $entityMgr)
    {
        $this->entityMgr = $entityMgr;

        return $this;
    }

    /**
     *
     * @param object $object
     */
    public function setObject($object)
    {
        $this->obj = $object;
    }

    /**
     *
     * @param integer $id
     * @return string
     */
    public function transform($id)
    {
        if (null === $id) {
            return "";
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
        } elseif (count($idsArray) > 1) {
            throw new \Exception('Too many ids');
        }

        return key($idsArray);
    }
}
