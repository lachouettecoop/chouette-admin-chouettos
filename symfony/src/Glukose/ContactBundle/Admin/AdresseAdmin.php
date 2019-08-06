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
            ->add('ligne1', 'textarea', array(
                'label' => 'Adresse',
                'attr' => array('placeholder' => '12 rue de la liberté')
            ))
            ->add('ligne2', null, array(
                'label' => "Complément d'adresse",
                'attr' => array('placeholder' => 'Bât B, appt 17')
            ))
            ->add('codePostal', null, array(
                'attr' => array('placeholder' => '31000')
            ))
            ->add('ville', null, array(
                'attr' => array('placeholder' => 'Ramonville Saint Agne')
            ))
            ->add('nPAI');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('ligne1')
            ->add('ligne2');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id');
    }
}