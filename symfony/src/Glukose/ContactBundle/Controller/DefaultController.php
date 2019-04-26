<?php

namespace Glukose\ContactBundle\Controller;

use Glukose\UserBundle\Entity\Adhesion;
use Glukose\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function addInscriptionAction(Request $request)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        if ($request->isMethod('POST')) {

            $user = new User();
            $now = new \DateTime('now');
            $user->setDateAdhesion($now->format('d/m/Y'));
            $user->setCivilite($request->request->get('civiliteC'));
            $user->setNom($request->request->get('nomC'));
            $user->setPrenom($request->request->get('prenomC'));
            $user->setUsername($request->request->get('emailC'));
            $user->setEmail($request->request->get('emailC'));
            $user->setTelephone($request->request->get('telC'));
            $dateNaissance = \DateTime::createFromFormat('d/m/Y', $request->request->get('dateNaissanceC'));
            $user->setDateNaissance($dateNaissance);
            $user->setMotDePasse($request->request->get('passwordC'));
            $user->setPlainPassword($request->request->get('passwordC'));

            $adhesion = new Adhesion();
            $adhesion->setDateAdhesion($now);
            $adhesion->setModePaiement($request->request->get('typePC'));
            $adhesion->setAnnee($request->request->get('anneeCC'));
            $adhesion->setMontant($request->request->get('montantCC'));

            $adhesion->setUser($user);
            $user->addAdhesion($adhesion);
            $user->setEnabled(true);

            $em->persist($adhesion);
            $em->persist($user);
            $em->flush();

            //add on LDAP
            $this->get('chouette.admin.ldap')->addUserOnLDAP($user);

        }

        return $this->render('GlukoseContactBundle:Default:index.html.twig');
    }
}
