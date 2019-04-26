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
            ->with('Civilité', array(
                'class'       => 'col-md-6'
            ))
            ->add('email')
            ->add('civilite', 'choice',
                  array('choices' => array(
                      'mme' => 'Madame',
                      'mr' => 'Monsieur'
                  )
                       ))
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('codeBarre')
            ->add('dateNaissance', 'sonata_type_date_picker', array(
                'required' => false,
                'format'=>'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                )
            ))
            ->add('enabled', null, array('required' => false, 'label' => 'Membre ?'))
            ->add('carteImprimee', null, array('required' => false, 'label' => 'Carte imprimée ?'))
            ->end()
            ->with('Association', array(
                'class'       => 'col-md-6'
            ))
            ->add('dateAdhesion', null, array('label' => 'Date première adhésion'))
            ->add('domaineCompetence', null, array('label' => 'Domaines de compétences'))
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

    public function getExportFields()
    {
        return array('id', 'civilite', 'nom','prenom', 'codebarre', 'email','exportDateNaissance', 'dateAdhesion', 'telephone','enabled', 'domaineCompetence', 'exportAdresse', 'exportAdresse1', 'exportAdresse2', 'exportAdresse3', 'exportAdresse4', 'exportAdresse5', 'exportAdresse6', 'adhesions', 'exportdAhesionAnnee', 'exportAdhesionDate', 'exportAdhesionMontant');
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('carteImprimee', null, ['label' => 'Carte imprimée ?'])
            ->add('enabled', null, ['label' => 'Actif ?'])
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
            ->add('notes', 'string', array('template' => 'GlukoseUserBundle:Admin:resetPassword.html.twig'))
            ->add('adhesions')
            ->add('enabled', null, array('label' => 'Activé', 'editable'=>true))
            ->add('carteImprimee', null, array('label' => 'Carte imprimée', 'editable'=>true))
            ;
    }

    public function getBatchActions()
    {
        $actions = [];
        if ($this->hasRoute('edit') && $this->isGranted('EDIT')) {
            $actions['imprimeCarte'] = array(
                'label' => 'Carte imprimée',
                'ask_confirmation' => true
            );

        }
        return $actions;
    }

    //make a copy of the existing object
    public function preUpdate($user)
    {
        $this->syncRelations($user);
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
            $user->setMotDePasse('123456'.(string)time());
        }
        if(empty($PasswordFOS)){
            $user->setPlainPassword('123456'.(string)time());
        }

        $username = $user->getUsername();
        if(empty($username)){
            $user->setUsername($user->getEmail());
        }
        $timestamp = time();
        if( empty($user->getCodeBarre()) ){
            $codeBarre = $this->generateEAN($timestamp);
            $user->setCodeBarre($codeBarre);
        }

        $this->syncRelations($user);
    }

    public function postPersist($user)
    {
        if($user->isEnabled()) {
            $this->ldapService->addUserOnLDAP($user);
        }
    }


    public function syncRelations($user){
        if($user->getAdhesions() != null){
            foreach($user->getAdhesions() as $adhesion){
                $adhesion->setUser($user);
            }
        }

    }

    function generateEAN($number)
    {
      $code = '24' . $number;
      $weightflag = true;
      $sum = 0;
      // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
      // loop backwards to make the loop length-agnostic. The same basic functionality
      // will work for codes of different lengths.
      for ($i = strlen($code) - 1; $i >= 0; $i--)
      {
        $sum += (int)$code[$i] * ($weightflag?3:1);
        $weightflag = !$weightflag;
      }
      $code .= (10 - ($sum % 10)) % 10;
      return $code;
    }






}
