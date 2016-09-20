<?php

namespace NS\UtilBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Doctrine\Common\Persistence\ObjectManager;
use NS\UtilBundle\Form\Transformers\EntityToAjaxJson;
use NS\UtilBundle\Form\Transformers\CollectionToAjaxJson;
use NS\UtilBundle\Form\Transformers\FormFieldToId;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of Autocomplete
 *
 * @author gnat
 */
class Autocomplete extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $entityMgr;

    /**
     * @var Router
     */
    private $router;

    /**
     * Autocomplete constructor.
     * @param ObjectManager $em
     * @param Router $router
     */
    public function __construct(ObjectManager $em, Router $router)
    {
        $this->entityMgr = $em;
        $this->router = $router;

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['use_datatransformer'])) {
            $transformer = new FormFieldToId($this->entityMgr, $options['class']);
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($transformer) {
                    $transformer->setObject($event->getForm()->getParent()->getData());
                }
            );
        } else {
            $transformer = ($options['collection'] == true) ? new CollectionToAjaxJson($this->entityMgr, $options['class']) : new EntityToAjaxJson($this->entityMgr, $options['class']);
        }

        $builder->addModelTransformer($transformer);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'tokenize' => true,
            'collection' => false,
            'invalid_message' => 'The selected entity does not exist',
            'attr' => array(
                'data-autocomplete' => "true",
                'data-autocomplete-href' => '',
                'data-autocomplete-tokenize' => "true",
                'data-autocomplete-multiple' => "false"
            ),
        ));

        $resolver->setRequired(array(
            'route',
            'collection',
            'class',
        ));

        $defined = array(
            'tokenize',
            'secondary-field',
            'use_datatransformer');

        if (method_exists($resolver, 'setDefined')) {
            $resolver->setDefined($defined);
        } else {
            $resolver->setOptional($defined);
        }
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ns_autocomplete';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-autocomplete-href'] = $this->router->generate($options['route']);

        if ($options['collection']) {
            $view->vars['attr']['data-autocomplete-multiple'] = "true";
        }

        if (isset($options['secondary-field'])) {
            $view->vars['attr']['data-autocomplete-secondary-field'] = json_encode($options['secondary-field']);
        }

        if ($options['tokenize'] == false) {
            $view->vars['attr']['data-autocomplete-tokenize'] = "false";
        }
    }
}
