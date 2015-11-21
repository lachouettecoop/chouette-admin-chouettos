<?php

namespace ChouetteCoop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Attribute;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Dn;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Sequence;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\ArrayField;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\DnPregMatch;
use Glukose\UserBundle\Entity\Account as Account;

class MainController extends Controller
{
    public function indexAction()
    {

        /*
        $a = new Account();
        $a->setUid('john.doo');
        $a->setFirstname('John');
        $a->setLastname('Doo');
        $a->setAlias(array('jdoo','j.doo'));
        $em = $this->get('gorg_ldap_orm.entity_manager');
        $em->persist($a);
        $em->flush();

        /*$repo = $em->getRepository('Gorg\Bundle\Application\Entity\Account');
        $a = $repo->findOneByUid('john.doo');*/

        /*$ds = ldap_connect($this->container->getParameter('ldapServerAdress'), 389);  // on suppose que le serveur LDAP est sur le serveur local
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($ds) {
            // Connexion avec une identité qui permet les modifications
            $r = ldap_bind($ds, $this->container->getParameter('ldapUser'), $this->container->getParameter('ldapMdp'));

            if ($r) {
                echo "Connexion LDAP réussie...";
            } else {
                echo "Connexion LDAP échouée...";
            }
            // Prépare les données            
            $info["objectclass"][0] = "posixAccount";
            $info["objectclass"][1] = "person";
            $info["objectclass"][2] = "mailAccount";
            $info["cn"] = "toto4@gmail.com";
            $info["sn"] = "marc";
            $info["description"] = "vincent";
            $info["mail"] = "toto4@gmail.com";
            $info["gidNumber"] = 1;
            $info["homeDirectory"] = "/test";
            $info["uid"] = "4";
            $info["uidNumber"] = 5;
            $info["userPassword"] = '{MD5}' . base64_encode(pack('H*',md5("123456")));
            //$info["gidNumber"] = "toto@gmail.com";
            

            // Ajoute les données au dossier
            $r = ldap_add($ds, "cn=toto4@gmail.com,dc=lachouettecoop,dc=fr", $info);

            ldap_close($ds);
        } else {
            echo "Impossible de se connecter au serveur LDAP";
        }*/       

        return $this->render('ChouetteCoopAdminBundle:Main:index.html.twig');
    }
}
