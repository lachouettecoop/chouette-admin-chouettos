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

            //->add('motDePasse')
            /*->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))*/
            ->with('Civilité', array(
                'class'       => 'col-md-6'
            ))
            ->add('email')
            ->add('civilite', 'choice',
                  array('choices' => array(
                      'mr' => 'Monsieur',
                      'mme' => 'Madame'
                      //,'mlle' => 'Mademoiselle' 
                  )
                       ))
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            //->add('portable')    
            ->add('enabled', null, array('required' => false, 'label' => 'Membre ?'))
            ->end()
            ->with('Association', array(
                'class'       => 'col-md-6'
            ))
            //->add('statusAssociatif')
            ->add('dateAdhesion', null, array('label' => 'Date première adhésion'))
            ->add('montant')
            ->add('modePaiement')
            //->add('presentAzendoo')
            ->add('csp', null, array('label' => 'Catégorie socio-profesionnelle'))
            ->add('notes')


            ->end()
            ->with('Historique des adhésions', array(
                'class'       => 'col-md-12'
            ))
            ->add('adhesions', 'sonata_type_collection', array(
                'required' => false,
            ),
                  array(
                      'edit' => 'inline',
                      'inline' => 'table',
                  )
                 )
            ->end()
            ->with('Adresse', array(
                'class'       => 'col-md-12'
            ))
            ->add('adresses', 'sonata_type_collection', array(
                'required' => false,
            ),
                  array(
                      'edit' => 'inline',
                      'inline' => 'table',
                  )
                 )
            ->end()
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
            ->add('groupes')
            ->add('adhesions')
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
        if($this->originalUserData['enabled'] == $user->isEnabled() && $user->isEnabled()){
            $this->ldapService->updateUserOnLDAP($user, $this->originalUserData);
        } elseif(!$user->isEnabled() && $this->originalUserData['enabled'] == true) {
            $this->ldapService->removeUserOnLDAP($user);
        } elseif(!$user->isEnabled()){
            
        } else {
            $this->ldapService->addUserOnLDAP($user);
        }
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
        if($user->isEnabled()) {
            $this->ldapService->addUserOnLDAP($user);
        }
    }



}