<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class PersonneRattacheeAdmin extends AbstractAdmin
{

    public function preUpdate($user)
    {
        $this->manageFileUpload($user);
    }
    public function prePersist($user)
    {
        $this->manageFileUpload($user);
    }
    private function manageFileUpload($user) {
        if ($user->getFile()) {
            $user->refreshUpdated();
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $user = $this->getSubject();

        $fileFieldOptions = array('required' => false);
        if ($user && ($webPath = $user->getPhoto())) {
            $fileFieldOptions['help'] = '<img src="/uploads/documents/'.$webPath.'" class="admin-preview" style="width: 300px;" />';
        }

        $formMapper
            ->with('Informations', array(
                'class' => 'col-md-6'
            ))

            ->add('nom')
            ->add('prenom')
            ->add('dateDeNaissance', DatePickerType::class, array(
                'label' => 'Date de naissance',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                )
            ))
            ->add('email')
            ->add('telephone')
            ->end()
            ->with('Photo', array(
                'class' => 'col-md-6'
            ))
            ->add('file', FileType::class, $fileFieldOptions)
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('nom')
            ->add('prenom')
            ->add('photo')
            ->add('dateDeNaissance')
            ->add('email')
            ->add('telephone')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('nom')
            ->add('prenom')
            ->add('photo')
            ->add('dateDeNaissance')
            ->add('email')
            ->add('telephone');
    }



    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('nom')
            ->add('prenom')
            ->add('photo')
            ->add('dateDeNaissance')
            ->add('email')
            ->add('telephone')
            ->add('updated')
            ;
    }
}
