<?php

namespace App\Admin;

use App\Entity\Task;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class TaskAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', null, array(
                'label' => 'Titre'
            ))
            ->add('description', null, array(
                'label' => 'Description'
            ))
            ->add('link', null, array(
                'label' => 'Lien'
            ))
            ->add('creneauGeneriques', EntityType::class, [
                'class' => 'App\Entity\CreneauGenerique',
                'choice_label' => 'titre', // ⚡ adapte selon ton champ (ex: title/nom/libelle)
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);

    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('title');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title')
            ->add('description')
            ->add('creneauGeneriques', null, [
                'associated_property' => 'titre', // ⚡ idem adapte
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('title')
            ->add('description')
             ->add('creneauGeneriques', null, [
                'associated_property' => 'titre',
            ]);
    }
}
