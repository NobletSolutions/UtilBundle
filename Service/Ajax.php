<?php
namespace NS\UtilBundle\Service;

//TODO abstract this out to not require the model manager but something that can return a repository for a class
use \NS\SecurityBundle\Model\Manager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Ajax
{
    private $_manager;
    private $_request;
    public function __construct(Manager $manager, Request $request)
    {
        $this->_manager = $manager;
        $this->_request = $request;
    }
    
    public function getAutocomplete($class,$alias,$field,$limit = 20)
    {
        $repo      = $this->_manager->getRepository($class);
        if(!$repo instanceof AjaxAutocompleteRepositoryInterface && !method_exists($repo, 'getForAutoComplete'))
            throw new \Exception("Repository for $class doesn't implement AjaxAutocompleteRepositoryInterface or provide getForAutoComplete function");
        
        $value     = $this->_request->get('q');
        $entities  = $repo->getForAutoComplete($alias,$field,$value,$limit);
        $json      = array();

        foreach ($entities as $entity)
        {
            if($entity instanceof AjaxAutocompleteInterface)
                $json[] = array(
                        'id'     => $entity->getId(),
                        'name'   => $entity->getAjaxDisplay(),
                    );
            else
                $json[] = array(
                        'id'     => $entity->getId(),
                        'name'   => $entity->__toString(),
                    );
        }
        
        $response = new Response();
        $response->setContent(json_encode($json));
        
        return $response;        
    }
}
