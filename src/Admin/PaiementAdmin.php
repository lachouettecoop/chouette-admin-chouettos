<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PaiementAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dateEcheance', DatePickerType::class, array(
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
                ChoiceType::class,
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