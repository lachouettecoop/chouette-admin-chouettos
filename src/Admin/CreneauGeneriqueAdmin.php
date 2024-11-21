<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeRangeFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class CreneauGeneriqueAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('titre')
            ->add('frequence', ChoiceType::class,
                ['choices' =>
                    [
                        'Modèle principal (A)' => '1',
                        'Modèle vacances (B)' => '2',
                        '1 semaine sur 4 (C)' => '3',
                    ],
                    'label' => 'Type'
                ]
            )
            ->add('jour', ChoiceType::class,
                ['choices' =>
                    [
                        'lundi' => '0',
                        'mardi' => '1',
                        'mercredi' => '2',
                        'jeudi' => '3',
                        'vendredi' => '4',
                        'samedi' => '5',
                        'dimanche' => '6',
                    ]
                ]
            )
            ->add('heureDebut')
            ->add('heureFin')
            ->add(
                'postes',
                CollectionType::class,
                array(
                    'required' => false,
                    'by_reference' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->add('actif')
            ->add('horsMag')
            ->add('demiPiaf')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('frequence', 'doctrine_orm_choice', array('label' => 'Type',
                'field_options' => array(
                    'required' => false,
                    'choices' => [
                        'Modèle principal (A)' => '1',
                        'Modèle vacances (B)' => '2',
                        '1 semaine sur 4 (C)' => '3',
                    ],
                ),
                'field_type' => ChoiceType::class,
                'show_filter' => true
            ))
            ->add('jour', 'doctrine_orm_choice', array('label' => 'Jour',
                'field_options' => array(
                    'required' => false,
                    'choices' => [
                        'lundi' => '0',
                        'mardi' => '1',
                        'mercredi' => '2',
                        'jeudi' => '3',
                        'vendredi' => '4',
                        'samedi' => '5',
                        'dimanche' => '6',
                    ],
                ),
                'field_type' => ChoiceType::class,
                'show_filter' => true
            ))
            ->add('titre', null, ['show_filter' => true])
            ->add('horsMag')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('jour', null, ['template' => 'admin/list_jour.html.twig'])
            ->add('heureDebut')
            ->add('heureFin')
            ->add('frequence', null, ['template' => 'admin/list_frequence.html.twig', 'label' => 'Type'])
            ->add('titre')
            ->add('horsMag')
            ->add('_action', null, [
                'actions' => [
                    'clone' => [
                        'template' => 'admin/list__action_clone.html.twig'
                    ],
                    'generate' => [
                        'template' => 'admin/form__action_generer.html.twig'
                    ]
                ]
            ]);
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
        $collection->add('generate', $this->getRouterIdParameter().'/generate');
    }


    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('frequence')
            ->add('jour')
            ->add('heureDebut')
            ->add('heureFin')
        ;
    }
}
