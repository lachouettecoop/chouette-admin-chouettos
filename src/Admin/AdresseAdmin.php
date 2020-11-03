<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class AdresseAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('ligne1')
            ->add('ligne2')
            ->add('nPAI')
            ->add('pays')
            ->add('codePostal')
            ->add('ville')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('ligne1')
            ->add('ligne2')
            ->add('nPAI')
            ->add('pays')
            ->add('codePostal')
            ->add('ville')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('ligne1', null, array(
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
            ));
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('ligne1')
            ->add('ligne2')
            ->add('nPAI')
            ->add('pays')
            ->add('codePostal')
            ->add('ville')
            ;
    }
}
