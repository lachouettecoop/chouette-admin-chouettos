<?php

namespace Glukose\ContactBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AdresseAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nom', 'textarea', array('label' => 'Nom adresse'))
            ->add('destinataire', 'textarea')
            ->add('ligne1', 'textarea')
            ->add('ligne2', 'textarea')
            ->add('ligne3', 'textarea')
            ->add('codePostal', 'textarea')
            ->add('ville', 'textarea')
            ->add('pays', 'textarea')
            ->add('nPAI')
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('ligne1')
            ->add('ligne2')
            ->add('ligne3')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nom')
            ;
    }
}