<?php
namespace NS\UtilBundle\Form\Transformers;

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
    
    private $_class;

    public function __construct(EntityManager $em,$class)
    {
        $this->_em    = $em;
        $this->_class = $class;
        
        return $this;
    }

    public function transform($entity)
    {
        if (null === $entity) {
            return "";
        }

        if (!$entity instanceof $this->_class)
            throw new UnexpectedTypeException($entity, $this->_class);
        
        $idsArray[$entity->getId()] = $entity->getAjaxDisplay();
        
        return json_encode($idsArray);
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
        
        return $this->_em->getRepository($this->_class)->find(key($idsArray));
    }
}
