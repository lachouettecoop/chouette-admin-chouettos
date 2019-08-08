<?php

namespace Glukose\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PaiementAdmin extends Admin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dateEcheance', 'sonata_type_date_picker', array(
                'required' => true,
                'label' => "Date d'échéance",
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => date('25/m/Y')
                )
            ))
            ->add('montant')
            ->add(
                'modePaiement',
                'choice',
                array('choices' => array(
                    'cheque' => 'cheque',
                    'especes' => 'especes',
                    'autre' => 'autre'
                ))
            )
            ->add('effectif')
            ->add('user'); // TODO Seulement quand standalone, pas sur la fiche User
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dateEcheance')
            ->add('effectif');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('user')
            ->addIdentifier('montant')
            ->addIdentifier('dateEcheance')
            ->addIdentifier('effectif')
        ;
    }

}