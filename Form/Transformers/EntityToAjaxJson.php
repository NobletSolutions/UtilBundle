<?php
namespace NS\UtilBundle\Form\Transformers;

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use NS\SecurityBundle\Model\Manager as EntityManager;

/**
 * Description of SuppliersToID
 *
 * @author gnat
 */
class EntityToAjaxJson implements DataTransformerInterface
{
    private $_em;
    
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
        
        return $this;
    }

    public function transform($entities)
    {
        if (null === $entities) {
            return "";
        }

        if (!$entities instanceof PersistentCollection) {
            throw new UnexpectedTypeException($entities, 'PersistentCollection');
        }
        
        $idsArray = array();
        foreach ($entities as $entity)
            $idsArray[$entity->getId()] = $entity->getAjaxDisplay();

        return json_encode($idsArray);
    }

    public function reverseTransform($ids)
    {
        if ('' === $ids || null === $ids)
            return new ArrayCollection();

        if (!is_string($ids))
            throw new UnexpectedTypeException($ids, 'string');
        
        $idsArray = json_decode($ids,true);

        return $this->_em->getRepository('NobletSolutionsNedcoBundle:Supplier')->getByIds(array_keys($idsArray));
    }
}
