<?php

namespace App\Controller;


class LdapController
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";
    const DN_GROUPES = "ou=groupes,o=lachouettecoop,dc=lachouettecoop,dc=fr";

    private $ldapServerAdress;
    private $ldapUser;
    private $ldapMdp;
    private $baseDn;
    private $ds;


    public function __construct(string $ldapDomain, string $ldapUsername, string $ldapPassword, string $ldapBaseDn)
    {
        $this->ldapServerAdress = $ldapDomain;
        $this->ldapUser = $ldapUsername;
        $this->ldapMdp = $ldapPassword;
        $this->baseDn = $ldapBaseDn;
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

    public function userAuth($user, $password){
        $this->connectToLdapAsAdmin();

        $r = ldap_bind($this->ds, $this->userDn($user), $password);
        if($r){
            return true;
        } else {
            return false;
        }
    }


    public function checkUser($user){
        $this->connectToLdapAsAdmin();

        $r = ldap_search($this->ds, $this->userDn($user), '(&(ObjectClass=person))');
        if($r){
            return true;
        } else {
            return false;
        }
    }

    public function getUser($user){
        $this->connectToLdapAsAdmin();


        $r = ldap_search($this->ds, $this->userDn($user), '(&(ObjectClass=person))');
        if($r) {
            return ldap_get_entries($this->ds, $r);
        } else{
            return false;
        }

    }

    private function userDn($email)
    {
        return "cn=" . $email . "," . self::DN_MEMBRES;
    }

    private function groupeDn($nom, $id)
    {
        $nomEchape = str_replace(" ", "\ ", $nom);
        return "cn=" . $nomEchape . "+gidNumber=" . $id . "," . self::DN_GROUPES;
    }

}
