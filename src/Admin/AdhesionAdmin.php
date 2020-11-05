<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdhesionAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dateAdhesion', DatePickerType::class, array(
                'required' => false,
                'format' => 'dd/MM/yyyy',
                /*'dp_min_date' => $start->format('m/d/Y'),
                'dp_max_date' => $end->format('m/d/Y'),*/
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => date('25/m/Y')
                )
            ))
            ->add('annee', null, array(
                'attr' => array('placeholder' => date('Y'))
            ))
            ->add('montant')
            ->add('modePaiement', ChoiceType::class,
                array('choices' => array(
                    'helloAsso' => 'helloAsso',
                    'cheque' => 'cheque',
                    'especes' => 'especes',
                    'cb' => 'cb',
                    'virement' => 'virement',
                    'sol violette' => 'solViolette',
                    'autre' => 'autre'
                )
                ))
            ->add('user');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dateAdhesion');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('dateAdhesion');
    }

}