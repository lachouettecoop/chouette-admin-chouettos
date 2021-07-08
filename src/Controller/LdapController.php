<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    /**
     * Add user to LDAP
     *
     * @param  User $user
     * @return boolean
     */
    public function addUserOnLDAP(User $user)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }

        $info = $this->ldapAdministrableInfosOfUser($user);
        $info["objectclass"][0] = "posixAccount";
        $info["objectclass"][1] = "person";
        $info["objectclass"][2] = "mailAccount";
        $info["gidNumber"] = 1;
        $info["homeDirectory"] = (string)$user->getCodeBarre();
        $info["uid"] = $user->getId();
        $info["uidNumber"] = $user->getId();
        $info["userPassword"] = '{MD5}' . base64_encode(pack('H*', md5($user->getMotDePasse())));

        // Ajoute le nouvel user dans LDAP
        $r = ldap_add($this->ds, $this->userDn($user->getEmail()), $info);

        if (!$r) {
            throw new \RuntimeException("Echec de l'ajout dans LDAP ...");
        }

        ldap_close($this->ds);

        return true;
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

    public function connectToLdapAsUser($user, $password)
    {
        $this->ds = ldap_connect($this->ldapServerAdress, 389);
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if (!$this->ds) {
            throw new \RuntimeException("Impossible de se connecter au serveur LDAP");
        }

        // Connexion avec une identité qui permet les modifications
        $r = ldap_bind($this->ds,$this->userDn($user),$password);
        if (!$r) {
            return false;
        }
        return true;
    }

    private function ldapAdministrableInfosOfUser(User $user)
    {
        $info["cn"] = $user->getEmail();
        $info["sn"] = $user->getNom();
        $info["description"] = $user->getPrenom();
        $info["mail"] = $user->getEmail();
        $info["homeDirectory"] = (string)$user->getCodeBarre();
        return $info;
    }

    private function userDn($email)
    {
        return "cn=" . $email . "," . self::DN_MEMBRES;
    }

    /**
     * remove user entry from LDAP
     *
     * @param  User $user
     * @return boolean
     */
    public function removeUserOnLDAP(User $user)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }

        // Supprime l'user dans LDAP
        $r = @ldap_delete($this->ds, $this->userDn($user->getEmail()));

        if (!$r) {
            throw new \RuntimeException("Echec de la suppression dans LDAP ...");
        }

        ldap_close($this->ds);

        return true;
    }


    /**
     * Update an user on the LDAP server
     *
     * @param   User $user
     * @param   array $originalUserData
     * @return boolean
     */
    public function updateUserOnLDAP(User $user, $originalUserData)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }

        $info = $this->ldapAdministrableInfosOfUser($user);

        if ($originalUserData != null) {
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
        } else {
            $currentCn = $info["mail"];
        }

        $r = ldap_modify($this->ds, $this->userDn($currentCn), $info);

        ldap_close($this->ds);

        return true;
    }

    /**
     * Update an user password ****** on the LDAP server
     *
     * @param   User $user
     * @return boolean
     */
    public function updateUserPassOnLDAP(User $user)
    {
        try {
            $this->connectToLdapAsAdmin();
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            return;
        }

        if ($user != null) {
            $currentCn = $user->getEmail();
            $info = [
                "userPassword" => '{MD5}' . base64_encode(pack('H*', md5($user->getMotDePasse())))
            ];
            $r = ldap_modify($this->ds, $this->userDn($currentCn), $info);
            ldap_close($this->ds);
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

    private function groupeDn($nom, $id)
    {
        $nomEchape = str_replace(" ", "\ ", $nom);
        return "cn=" . $nomEchape . "+gidNumber=" . $id . "," . self::DN_GROUPES;
    }

}
