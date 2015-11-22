<?php

namespace Glukose\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class GroupeAdmin extends Admin
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";

    private $originalGroupeData;
    private $ldapService;

    public function setLdapService($ldapService){
        $this->ldapService = $ldapService;
    }

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper            
            ->add('nom')
            ->add('membres', 'sonata_type_model_autocomplete', array(
                'class' => 'Glukose\UserBundle\Entity\User',
                'property' => 'prenom',
                'placeholder' => 'Taper les première lettres du prenom',
                'multiple' => true,
                'required' => false)
                 )
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('nom')
            ;
    }


    public function postPersist($groupe)
    {
        $this->ldapService->addGroupeOnLDAP($groupe);
    }

    //make a copy of the existing object
    public function preUpdate($groupe)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $this->originalGroupeData = $em->getUnitOfWork()->getOriginalEntityData($groupe);
    }

    public function postUpdate($groupe)
    {
        //ldap_rename si le nom du groupe a changé
        if($this->originalGroupeData['nom'] != $groupe->getNom()){
            $this->ldapService->updateGroupeOnLDAP($groupe, $this->originalGroupeData);
        }
        
        $this->ldapService->updateGroupeMembersOnLDAP($groupe, $this->originalGroupeData);        

    }




}