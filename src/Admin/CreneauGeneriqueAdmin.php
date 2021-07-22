<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            ->add('frequence', ChoiceType::class,
                ['choices' =>
                    [
                        'A' => '1',
                        'B' => '2',
                        'C' => '3',
                        'D' => '4',
                    ],
                    'label' => 'Semaine / fréquence'
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
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->add('actif')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('frequence', 'doctrine_orm_choice', array('label' => 'Semaine / fréquence',
                'field_options' => array(
                    'required' => false,
                    'choices' => [
                        'A' => '1',
                        'B' => '2',
                        'C' => '3',
                        'D' => '4',
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
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('frequence', null, ['template' => 'admin/list_frequence.html.twig'])
            ->add('jour', null, ['template' => 'admin/list_jour.html.twig'])
            ->add('heureDebut')
            ->add('heureFin')
            ;
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
