<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
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
                    ]
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
            ->add('frequence')
            ->add('jour')
            ->add('heureDebut')
            ->add('heureFin')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('frequence')
            ->add('jour')
            ->add('heureDebut')
            ->add('heureFin')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
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
