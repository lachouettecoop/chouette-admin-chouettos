<?php

namespace Glukose\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class GroupeAdmin extends Admin
{
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

    public function preUpdate($groupe)
    {

        //delete
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $original = $em->getUnitOfWork()->getOriginalEntityData($groupe);

        $ds = ldap_connect($this->getConfigurationPool()->getContainer()->getParameter('ldapServerAdress'), 389);  // on suppose que le serveur LDAP est sur le serveur local
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($ds) {
            // Connexion avec une identité qui permet les modifications
            $r = ldap_bind($ds, $this->getConfigurationPool()->getContainer()->getParameter('ldapUser'), $this->getConfigurationPool()->getContainer()->getParameter('ldapMdp'));
            $r = ldap_delete($ds, "cn=".$original['nom']."+gidNumber=".$original['id'].",ou=groupes,o=lachouettecoop,dc=lachouettecoop,dc=fr");            
            $info["objectclass"][0] = "posixGroup";
            $info["objectclass"][1] = "top";
            $info["cn"] = $groupe->getNom();
            $info["gidNumber"] = $groupe->getId();

            // Ajoute les données au dossier
            $r = ldap_add($ds, "cn=".$groupe->getNom()."+gidNumber=".$groupe->getId().",ou=groupes,o=lachouettecoop,dc=lachouettecoop,dc=fr", $info);
            
            foreach($groupe->getMembres() as $membre){
                $info = array();
                $info["memberUid"] = $membre->getId();
                $r = ldap_mod_add($ds, "cn=".$groupe->getNom()."+gidNumber=".$groupe->getId().",ou=groupes,o=lachouettecoop,dc=lachouettecoop,dc=fr", $info);

            }
            ldap_close($ds);
        } else {
            echo "Impossible de se connecter au serveur LDAP";
        }

    }

    public function prePersist($groupe)
    {

    }

    public function postPersist($groupe)
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
            $info["objectclass"][0] = "posixGroup";
            $info["objectclass"][1] = "top";
            $info["cn"] = $groupe->getNom();
            $info["gidNumber"] = $groupe->getId();

            // Ajoute les données au dossier
            $r = ldap_add($ds, "cn=".$groupe->getNom()."+gidNumber=".$groupe->getId().",ou=groupes,o=lachouettecoop,dc=lachouettecoop,dc=fr", $info);

            ldap_close($ds);
        } else {
            echo "Impossible de se connecter au serveur LDAP";
        }

    }


}