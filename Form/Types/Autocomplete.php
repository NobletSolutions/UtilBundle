<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use NS\SecurityBundle\Model\Manager as EntityManager;
use \NS\UtilBundle\Form\Transformers\EntityToAjaxJson;
use \NS\UtilBundle\Form\Transformers\CollectionToAjaxJson;

/**
 * Description of Autocomplete
 *
 * @author gnat
 */
class Autocomplete extends AbstractType
{
    private $_em;
    private $_router;

    public function __construct(EntityManager $em, Router $router)
    {
        $this->_em     = $em;
        $this->_router = $router;
        
        return $this;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = ($options['collection'] == true) ? new CollectionToAjaxJson($this->_em,$options['class']) : new EntityToAjaxJson($this->_em,$options['class']) ;
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'collection'       => false,
            'invalid_message'  => 'The selected entity does not exist',
            'attr'             => array(
                                    'data-autocomplete'          => "true",
                                    'data-autocomplete-href'     => '',
                                    'data-autocomplete-tokenize' => "true",
                                    'data-autocomplete-multiple' => "false"
                                       ),
        ));
        
        $resolver->setRequired(array(
            'route',
            'collection',
            'class',
        ));
        
        $resolver->setOptional(array('secondary-field'));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'ns_autocomplete';
    }

    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-autocomplete-href'] = $this->_router->generate($options['route']);
        
        if($options['collection'])
            $view->vars['attr']['data-autocomplete-multiple'] = "true";
        
        if(isset($options['secondary-field']))
            $view->vars['attr']['data-autocomplete-secondary-field'] = json_encode($options['secondary-field']);
    }
}
