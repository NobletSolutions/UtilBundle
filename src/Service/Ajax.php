<?php

namespace NS\UtilBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Ajax
{
    /** @var EntityManagerInterface */
    private $entityMgr;

    /** @var RequestStack */
    private $requestStack;

    /** @var Environment */
    private $twig;

    /**
     * @var string  Defaults to NSUtilBundle:Ajax:autocomplete.json.twig
     */
    private $template;

    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack, Environment $twig, string $template)
    {
        $this->entityMgr    = $manager;
        $this->requestStack = $requestStack;
        $this->twig         = $twig;
        $this->template     = $template;
    }

    /**
     * @param string      $class
     * @param array|mixed $fields
     * @param int         $limit
     *
     * @return Response
     * @throws \Exception
     */
    public function getAutocomplete(string $class, $fields, int $limit = 20)
    {
        $repo = $this->entityMgr->getRepository($class);

        if (!$repo instanceof AjaxAutocompleteRepositoryInterface) {
            throw new \RuntimeException("$class Repository doesn't implement AjaxAutocompleteRepositoryInterface");
        }

        /** @var Request|null $request */
        $request   = $this->requestStack->getCurrentRequest();
        if(!$request) {
            return new Response();
        }
        $secondary = $request->get('secondary-field');
        $v         = $request->request->get('value');

        if (empty($v)) {
            $v = $request->get('q');
        }

        $st        = json_decode($secondary, true);
        $secondary = $st ?: $secondary;
        $value     = (!empty($secondary)) ? ['value' => $v, 'secondary' => $secondary] : ['value' => $v];
        $entities  = $repo->getForAutoComplete($fields, $value, $limit)->getResult();
        $content   = $this->twig->render($this->template, ['entities' => $entities]);

        return new Response($content);
    }
}
