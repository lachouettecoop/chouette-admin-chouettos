<?php

namespace Glukose\ContactBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AdresseAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('nom', 'textarea', array('label' => 'Nom adresse'))
            ->add('destinataire', 'textarea')
            ->add('ligne1', 'textarea')
            ->add('ligne2', 'textarea')
            ->add('ligne3', 'textarea')
            ->add('codePostal', 'textarea')
            ->add('ville', 'textarea')
            //->add('pays', 'country')
            ->add('nPAI');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('ligne1')
            ->add('ligne2')
            ->add('ligne3');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('destinataire');
    }
}