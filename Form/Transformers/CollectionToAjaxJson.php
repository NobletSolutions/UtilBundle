<?php
namespace NS\UtilBundle\Form\Transformers;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Description of SuppliersToID
 *
 * @author gnat
 */
class CollectionToAjaxJson implements DataTransformerInterface
{
    private $_em;

    private $_class;

    public function __construct(ObjectManager $em,$class)
    {
        $this->_em    = $em;
        $this->_class = $class;

        return $this;
    }

    public function transform($entities)
    {
        if (null === $entities || empty($entities))
            return "";

        if (!$entities instanceof PersistentCollection && !$entities instanceof ArrayCollection)
            throw new UnexpectedTypeException($entities, 'PersistentCollection or ArrayCollection');

        $idsArray = array();
        // check for interface...
        foreach ($entities as $entity)
            $idsArray[$entity->getId()] = $entity->getAjaxDisplay();

        if(empty($idsArray))
            return null;

        return json_encode($idsArray);
    }

    public function reverseTransform($ids)
    {
        if ('' === $ids || null === $ids)
            return new ArrayCollection();

        if (!is_string($ids))
            throw new UnexpectedTypeException($ids, 'string');

        $idsArray = json_decode($ids,true);

        if(empty($idsArray))
            return null;

        return $this->_em->getRepository($this->_class)->getByIds(array_keys($idsArray));
    }
}
