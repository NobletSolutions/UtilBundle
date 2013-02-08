<?php
namespace NS\UtilBundle\Service;

//TODO abstract this out to not require the model manager but something that can return a repository for a class
use \NS\SecurityBundle\Model\Manager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\TwigBundle\TwigEngine;

class Ajax
{
    private $_manager;
    
    private $_request;
    
    private $_templating;
    
    public function __construct(Manager $manager, Request $request, TwigEngine $templating)
    {
        $this->_manager    = $manager;
        $this->_request    = $request;
        $this->_templating = $templating;
    }
    
    public function getAutocomplete($class, $alias, $fields, $limit = 20)
    {
        $repo = $this->_manager->getRepository($class);
        
        if(!$repo instanceof AjaxAutocompleteRepositoryInterface)
            throw new \Exception("$class Repository doesn't implement AjaxAutocompleteRepositoryInterface");
        
        $secondary = $this->_request->get('secondary-field');
        $v         = $this->_request->request->get('value');

        if(empty($v))
            $v = $this->_request->get('q');
        
        $value = (!empty($secondary))? array('value'=>$v,'secondary'=> json_decode($secondary,true)): array('value'=>$v);

        $entities  = $repo->getForAutoComplete($alias,$fields,$value,$limit)->getResult();
        $content   = $this->_templating->render('NSUtilBundle:Ajax:autocomplete.html.twig',array('entities'=>$entities));
        
        $r         = new \Symfony\Component\HttpFoundation\Response();
        $r->setContent($content);
        
        return $r;
    }
}
