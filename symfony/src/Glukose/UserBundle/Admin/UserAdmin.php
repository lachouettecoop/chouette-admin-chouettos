<?php

namespace Glukose\UserBundle\Admin;

use Glukose\UserBundle\Entity\User;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";
    
    private $originalUserData;
    private $ldapService;     
    
    public function setLdapService($ldapService){
        $this->ldapService = $ldapService;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('username')
            ->add('email')
            //->add('motDePasse')
            /*->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))*/
            ->add('civilite', 'choice',
                  array('choices' => array(
                      'mr' => 'Monsieur',
                      'mme' => 'Madame',
                      'mlle' => 'Mademoiselle' )
                       ))
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('portable')    
            ->add('enabled', null, array('required' => false, 'label' => 'Membre ?'))    
            //->add('fax')
            /*->add('adresses', 'sonata_type_collection', array(
                'required' => false,
            ),
                  array(
                      'edit' => 'inline',
                      'inline' => 'table',
                  )
                 )*/
            ;
    }

    
    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('email')
            ->addIdentifier('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('enabled', null, array('label' => 'Activé', 'editable'=>true))
            ;
    }

    //make a copy of the existing object
    public function preUpdate($user)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $this->originalUserData = $em->getUnitOfWork()->getOriginalEntityData($user);
    }

    //update the user on ldap
    public function postUpdate($user)
    {
        $this->ldapService->updateUserOnLDAP($user, $this->originalUserData);        
    }

    
    public function prePersist($user)
    {
        $PasswordLDAP = $user->getMotDePasse();
        $PasswordFOS = $user->getPassword();
        if(empty($PasswordLDAP)){
            $user->setMotDePasse('123456');
        }
        if(empty($Password)){
            $user->setPlainPassword('123456');
        }

        $username = $user->getUsername();
        if(empty($username)){
            $user->setUsername($user->getEmail());
        }
    }
    
    public function postPersist($user)
    {
        $this->ldapService->addUserOnLDAP($user);
    }



}