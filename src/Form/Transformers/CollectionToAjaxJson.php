<?php
namespace NS\UtilBundle\Form\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class CollectionToAjaxJson implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityMgr;

    /**
     * @var string
     */
    private $className;

    /**
     * CollectionToAjaxJson constructor.
     * @param EntityManagerInterface $em
     * @param $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        $this->entityMgr = $em;
        $this->className = $class;
    }

    /**
     * @param mixed $entities
     * @return null|string
     */
    public function transform($entities)
    {
        if (null === $entities || empty($entities)) {
            return "";
        }

        if (!$entities instanceof PersistentCollection && !$entities instanceof ArrayCollection) {
            throw new UnexpectedTypeException($entities, 'PersistentCollection or ArrayCollection');
        }

        $idsArray = array();
        // check for interface...
        foreach ($entities as $entity) {
            $idsArray[$entity->getId()] = $entity->getAjaxDisplay();
        }

        if(empty($idsArray)) {
            return null;
        }

        return json_encode($idsArray);
    }

    /**
     * @param mixed $ids
     * @return ArrayCollection|null
     */
    public function reverseTransform($ids)
    {
        if ('' === $ids || null === $ids) {
            return new ArrayCollection();
        }

        if (!is_string($ids)) {
            throw new UnexpectedTypeException($ids, 'string');
        }

        $idsArray = json_decode($ids,true);

        if(empty($idsArray)) {
            return null;
        }

        return $this->entityMgr->getRepository($this->className)->getByIds(array_keys($idsArray));
    }
}
