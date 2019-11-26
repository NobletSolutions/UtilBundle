<?php
namespace NS\UtilBundle\Form\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use NS\UtilBundle\Service\AjaxAutocompleteInterface;

class EntityToAjaxJson implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityMgr;

    /** @var string */
    private $className;

    /**
     * @param EntityManagerInterface $em
     * @param string $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        $this->entityMgr    = $em;
        $this->className = $class;
    }

    /**
     * @param mixed $entity
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return "";
        }

        if (!$entity instanceof $this->className) {
            throw new UnexpectedTypeException($entity, $this->className);
        }
        
        if (!$entity instanceof AjaxAutocompleteInterface) {
            throw new UnexpectedTypeException($entity, 'AjaxAutocompleteInterface');
        }
        
        $idArray = array($entity->getId() => $entity->getAjaxDisplay());

        return json_encode($idArray);
    }

    /**
     * @param mixed $ids
     * @return null|object
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
        
        $idsArray = json_decode($ids,true);

        if(empty($idsArray)) {
            return null;
        } else if(count($idsArray) > 1) {
            throw new \Exception('Too many ids');
        }
        
        return $this->entityMgr->getRepository($this->className)->find(key($idsArray));
    }
}
