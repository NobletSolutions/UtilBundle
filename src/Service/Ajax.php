<?php
namespace NS\UtilBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

class Ajax
{
    /**
     * @var ObjectManager
     */
    private $entityMgr;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TwigEngine
     */
    private $twig;

    /**
     * @var string Defaults to NSUtilBundle:Ajax:autocomplete.json.twig
     */
    private $template;

    /**
     * Ajax constructor.
     * @param ObjectManager $manager
     * @param RequestStack $requestStack
     * @param TwigEngine $twig
     * @param string $template
     */
    public function __construct(ObjectManager $manager, RequestStack $requestStack, TwigEngine $twig, $template)
    {
        $this->entityMgr    = $manager;
        $this->requestStack = $requestStack;
        $this->twig         = $twig;
        $this->template     = $template;
    }

    /**
     * @param $class
     * @param $fields
     * @param int $limit
     * @return Response
     * @throws \Exception
     * @throws \Twig_Error
     */
    public function getAutocomplete($class, $fields, $limit = 20)
    {
        $repo = $this->entityMgr->getRepository($class);

        if(!$repo instanceof AjaxAutocompleteRepositoryInterface) {
            throw new \RuntimeException("$class Repository doesn't implement AjaxAutocompleteRepositoryInterface");
        }

        $request   = $this->requestStack->getCurrentRequest();
        $secondary = $request->get('secondary-field');
        $v         = $request->request->get('value');

        if(empty($v)) {
            $v = $request->get('q');
        }

        $st       = json_decode($secondary,true);
        $secondary= (($st)?$st:$secondary);
        $value    = (!empty($secondary))? array('value' => $v,'secondary' => $secondary) : array('value' => $v);
        $entities = $repo->getForAutoComplete($fields,$value,$limit)->getResult();
        $content  = $this->twig->render($this->template,array('entities'=>$entities));

        return new Response($content);
    }
}
