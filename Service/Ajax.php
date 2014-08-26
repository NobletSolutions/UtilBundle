<?php
namespace NS\UtilBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

class Ajax
{
    private $_manager;
    
    private $_request;
    
    private $_templating;
    
    private $_useTemplate;
    
    public function __construct(ObjectManager $manager, Request $request, TwigEngine $templating)
    {
        $this->_manager     = $manager;
        $this->_request     = $request;
        $this->_templating  = $templating;
        $this->_useTemplate = true;
    }
    
    public function setUseTemplate($useTemplate = true)
    {
        $this->_useTemplate = $useTemplate;
        return $this;
    }
    
    public function getAutocomplete($class, $fields, $limit = 20)
    {
        $repo = $this->_manager->getRepository($class);
        
        if(!$repo instanceof AjaxAutocompleteRepositoryInterface)
            throw new \Exception("$class Repository doesn't implement AjaxAutocompleteRepositoryInterface");
        
        $secondary = $this->_request->get('secondary-field');
        $v         = $this->_request->request->get('value');

        if(empty($v))
            $v = $this->_request->get('q');

        $st       = json_decode($secondary,true);
        $secondary= (($st)?$st:$secondary);
        $value    = (!empty($secondary))? array('value' => $v,'secondary' => $secondary) : array('value' => $v);
        $entities = $repo->getForAutoComplete($fields,$value,$limit)->getResult();
        
        if($this->_useTemplate)
        {
            $content  = $this->_templating->render('NSUtilBundle:Ajax:autocomplete.html.twig',array('entities'=>$entities));
            return new Response($content);
        }
        else
        {
            $out = array();
            foreach($entities as $id => $entity)
            {
                if(method_exists($entity, 'getAjaxDisplay'))
                    $entityS = $entity->getAjaxDisplay();
                else
                    $entityS = $entity->__toString();
                
                $out[] = array('id'=>$entity->getId(), 'name'=>$entityS);
            }
            
            $response = new Response(json_encode($out));
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }
    }
}
