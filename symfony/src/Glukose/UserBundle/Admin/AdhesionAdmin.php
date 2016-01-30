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
            ->add('dateAdhesion', 'sonata_type_date_picker', array(
                'required' => false,
                'format'=>'dd/MM/yyyy',
                /*'dp_min_date' => $start->format('m/d/Y'),
                'dp_max_date' => $end->format('m/d/Y'),*/
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                )
            ))
            ->add('annee')
            ->add('montant')
            ->add('modePaiement', 'choice',
                  array('choices' => array(
                      'helloAsso' => 'helloAsso',
                      'cheque' => 'cheque',
                      'especes' => 'especes',
                      'cb' => 'cb',
                      'virement' => 'virement',
                      'solViolette' => 'sol violette',
                      'autre' => 'autre'
                  )
                       ))
            ->add('user')
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