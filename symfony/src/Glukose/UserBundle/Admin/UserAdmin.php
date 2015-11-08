<?php

namespace Glukose\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{
    // Fields to be shown on create/edit forms
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

    public function preUpdate($user)
    {
        //delete
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $original = $em->getUnitOfWork()->getOriginalEntityData($user);

        $ds = ldap_connect($this->getConfigurationPool()->getContainer()->getParameter('ldapServerAdress'), 389);  // on suppose que le serveur LDAP est sur le serveur local
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($ds) {
            // Connexion avec une identité qui permet les modifications
            $r = ldap_bind($ds, $this->getConfigurationPool()->getContainer()->getParameter('ldapUser'), $this->getConfigurationPool()->getContainer()->getParameter('ldapMdp'));
            $r = ldap_delete($ds, "cn=".$original['email'].",ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr");            
        }
        ldap_close($ds);
        $this->postPersist($user);

    }

    public function prePersist($user)
    {
        $user->setEnabled(true);
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

        $ds = ldap_connect($this->getConfigurationPool()->getContainer()->getParameter('ldapServerAdress'), 389);  // on suppose que le serveur LDAP est sur le serveur local
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($ds) {
            // Connexion avec une identité qui permet les modifications
            $r = ldap_bind($ds, $this->getConfigurationPool()->getContainer()->getParameter('ldapUser'), $this->getConfigurationPool()->getContainer()->getParameter('ldapMdp'));

            if ($r) {
                echo "Connexion LDAP réussie...";
            } else {
                echo "Connexion LDAP échouée...";
            }
            // Prépare les données            
            $info["objectclass"][0] = "posixAccount";
            $info["objectclass"][1] = "person";
            $info["objectclass"][2] = "mailAccount";
            $info["cn"] = $user->getEmail();
            $info["sn"] = $user->getNom();
            $info["description"] = $user->getPrenom();
            $info["mail"] = $user->getEmail();
            $info["gidNumber"] = 1;
            $info["homeDirectory"] = "/test";
            $info["uid"] = $user->getId();
            $info["uidNumber"] = $user->getId();
            $info["userPassword"] = '{MD5}' . base64_encode(pack('H*',md5($user->getMotDePasse())));
            //$info["gidNumber"] = "toto@gmail.com";


            // Ajoute les données au dossier
            $r = ldap_add($ds, "cn=".$user->getEmail().",ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr", $info);

            ldap_close($ds);
        } else {
            echo "Impossible de se connecter au serveur LDAP";
        }

    }


}