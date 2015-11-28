<?php

namespace Glukose\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AdhesionAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper            
            ->add('dateAdhesion')
            ->add('montant')
            ->add('modePaiement', 'choice',
                  array('choices' => array(
                      'cheque' => 'cheque',
                      'especes' => 'especes',
                      'cb' => 'cb',
                      'virement' => 'virement' )
                       ))
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dateAdhesion')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('dateAdhesion')
            ;
    }


}