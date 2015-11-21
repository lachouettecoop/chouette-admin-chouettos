<?php
// src/ChouetteCoop/AdminBundle/LDAP/LDAPService.php

namespace ChouetteCoop\AdminBundle\LDAP;

use Glukose\UserBundle\Entity\User as User;

class LDAPService
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";

    private $ldapServerAdress;
    private $ldapUser;
    private $ldapMdp;
    private $ds;


    public function __construct($ldapServerAdress, $ldapUser, $ldapMdp)
    {

        $this->ldapServerAdress = $ldapServerAdress;
        $this->ldapUser = $ldapUser;
        $this->ldapMdp = $ldapMdp;

    }

    private function connectToLdapAsAdmin()
    {
        $this->ds = ldap_connect($this->ldapServerAdress, 389);
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if (!$this->ds) {
            throw new \RuntimeException("Impossible de se connecter au serveur LDAP");
        }

        // Connexion avec une identité qui permet les modifications
        $r = ldap_bind($this->ds, $this->ldapUser, $this->ldapMdp);
        if (!$r) {
            throw new \RuntimeException("Connexion LDAP échouée...");
        }
        
        return true;
    }
    
    private function ldapAdministrableInfosOfUser(User $user)
    {
        $info["cn"] = $user->getEmail();
        $info["sn"] = $user->getNom();
        $info["description"] = $user->getPrenom();
        $info["mail"] = $user->getEmail();
        return $info;
    }

    private function userDn($email)
    {
        return "cn=" . $email . "," . self::DN_MEMBRES;
    }
    
    public function addUserOnLDAP(User $user)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch(\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }
        
        $info = $this->ldapAdministrableInfosOfUser($user);
        $info["objectclass"][0] = "posixAccount";
        $info["objectclass"][1] = "person";
        $info["objectclass"][2] = "mailAccount";
        $info["gidNumber"] = 1;
        $info["homeDirectory"] = "/test";
        $info["uid"] = $user->getId();
        $info["uidNumber"] = $user->getId();
        $info["userPassword"] = '{MD5}' . base64_encode(pack('H*',md5($user->getMotDePasse())));
        
         // Ajoute le nouvel user dans LDAP
        $r = ldap_add($this->ds, $this->userDn($user->getEmail()), $info);
        
        if (!$r) {
            throw new \RuntimeException("Echec de l'ajout dans LDAP ...");
        }

        ldap_close($this->ds);
        
        return true;
    }
    
    public function updateUserOnLDAP(User $user, $originalUserData)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch(\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }
        
        $info = $this->ldapAdministrableInfosOfUser($user);        
        $currentCn = $originalUserData['email'];
        
        //on vérifie d'abord si l'email change, car c'est l'id sur LDAP et la
        // méthode php est ldsp_rename
        if ($info['cn'] != $currentCn) {
            if (false === ldap_rename($this->ds, $this->userDn($currentCn), "cn=" . $info['cn'], self::DN_MEMBRES, true)) {
                throw new \RuntimeException('Impossible de modifier l\'email');
            }
            $currentCn = $info['cn'];
            unset($info['cn']);
        }
        $r = ldap_modify($this->ds, $this->userDn($currentCn), $info);

        ldap_close($this->ds);
        
        return true;
    }

}