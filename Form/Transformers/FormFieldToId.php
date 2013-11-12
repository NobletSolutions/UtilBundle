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
    private $_em;

    private $_object;

    public function __construct(ObjectManager $em)
    {
        $this->_em   = $em;

        return $this;
    }

    public function setObject($object)
    {
        $this->_object = $object;
    }

    public function transform($id)
    {
        if (null === $id)
            return "";

        // This should probably test for an interface
        // We should also make what method we are looking for
        if(is_object($this->_object) && method_exists($this->_object, 'getType'))
            $entity = $this->_em->getRepository($this->_object->getType()->getClassMatch())->find($id);
        else
            $entity = $this->_object;

        return json_encode(array($id=>$entity.''));
    }

    public function reverseTransform($ids)
    {
        if ('' === $ids || null === $ids)
            return null;

        if (!is_string($ids))
            throw new UnexpectedTypeException($ids, 'string');

        $idsArray = json_decode($ids,true);

        if(empty($idsArray))
            return null;
        else if(count($idsArray) > 1)
            throw new \Exception('Too many ids');

        return key($idsArray);
    }
}
