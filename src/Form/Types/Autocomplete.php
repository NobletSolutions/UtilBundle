<?php

namespace NS\UtilBundle\Form\Types;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use NS\UtilBundle\Form\Transformers\EntityToAjaxJson;
use NS\UtilBundle\Form\Transformers\CollectionToAjaxJson;
use NS\UtilBundle\Form\Transformers\FormFieldToId;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class Autocomplete extends AbstractType
{
    /** @var EntityManagerInterface */
    private $entityMgr;

    /** @var RouterInterface */
    private $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->entityMgr = $em;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['use_datatransformer'])) {
            $transformer = new FormFieldToId($this->entityMgr, $options['class']);
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                static function (FormEvent $event) use ($transformer) {
                    $transformer->setObject($event->getForm()->getParent()->getData());
                }
            );
        } else {
            $transformer = ($options['collection'] === true) ? new CollectionToAjaxJson($this->entityMgr, $options['class']) : new EntityToAjaxJson($this->entityMgr, $options['class']);
        }

        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'tokenize' => true,
            'collection' => false,
            'invalid_message' => 'The selected entity does not exist',
            'attr' => array(
                'data-autocomplete' => 'true',
                'data-autocomplete-href' => '',
                'data-autocomplete-tokenize' => 'true',
                'data-autocomplete-multiple' => 'false'
            ),
        ));

        $resolver->setRequired(array(
            'route',
            'collection',
            'class',
        ));

        $resolver->setDefined(array(
            'tokenize',
            'secondary-field',
            'use_datatransformer'));
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-autocomplete-href'] = $this->router->generate($options['route']);

        if ($options['collection']) {
            $view->vars['attr']['data-autocomplete-multiple'] = 'true';
        }

        if (isset($options['secondary-field'])) {
            $view->vars['attr']['data-autocomplete-secondary-field'] = json_encode($options['secondary-field']);
        }

        if ($options['tokenize'] === false) {
            $view->vars['attr']['data-autocomplete-tokenize'] = 'false';
        }
    }
}
