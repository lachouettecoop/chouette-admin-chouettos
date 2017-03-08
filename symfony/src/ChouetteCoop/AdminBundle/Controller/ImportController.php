<?php

namespace ChouetteCoop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Glukose\UserBundle\Entity\User;


class ImportController extends Controller
{

  /**
   * Disable users who have not an adhesion for $annee
   *
   * @param  Request $request
   * @return View
   */
  public function desactivationMembresAction($annee, Request $request)
  {
        $em = $this
          ->getDoctrine()
          ->getManager();

        $userRep = $em->getRepository('GlukoseUserBundle:User');

        $users = $userRep->findAll();

        foreach ($users as $user) {

          $flag = false;
          foreach ($user->getAdhesions() as $adhesion) {
            if($adhesion->getAnnee() == $annee) {
              $flag = true;
            }
          }

          if(!$flag){
            $user->setEnabled(false);
            $em->persist($user);
          }
        }

        $em->flush();
        $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('ChouetteCoopAdminBundle:Main:index.html.twig',
                             array('admin_pool' => $admin_pool)
                            );
  }

    /**
     * Import CRM abonnes
     * @param  Request $request
     * @return View
     */
    public function importMembresAction(Request $request)
    {

        $em = $this
            ->getDoctrine()
            ->getManager();

        $ldapService = $this->get('chouette.admin.ldap');

        $repositoryU = $em->getRepository('GlukoseUserBundle:User');

        $userManager = $this->container->get('fos_user.user_manager');

        $path[] = array();

        $form = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'Fichier CSV'))
            ->getForm();

        // Check if we are posting stuff
        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->handleRequest($request);

            // If form is valid
            if ($form->isValid()) {
                // Get file
                $data = $form->getData();


                $file = $form->get('submitFile');

                // Your csv file here when you hit submit button
                $pathFile  = $file->getData();


                if (($handle = fopen($pathFile->getRealPath(), "r")) !== FALSE) {
                    while(($row = fgetcsv($handle)) !== FALSE) {

                            if(filter_var($row[4], FILTER_VALIDATE_EMAIL) != false){
                                $user = $userManager->createUser();

                                $user->setUsername($row[4]);
                                $user->setEmail($row[4]);
                                $user->setNom($row[2]);
                                $user->setPrenom($row[3]);
                                $user->setTelephone($row[5]);
                                $user->setPlainPassword('123456666');
                                $user->setMotDePasse('123456666');

                                $user->setStatusAssociatif($row[6]);
                                $user->setEnabled(false);
                                if($row[6] == 'membre'){
                                    $user->setEnabled(true);
                                }

                                $user->setDateAdhesion($row[7]);
                                $user->setTypeCotisation($row[8]);
                                $user->setMontant($row[9]);
                                $user->setModePaiement($row[10]);
                                $user->setPresentAzendoo($row[11]);
                                $user->setDateAzendoo($row[12]);
                                $user->setSiEchec($row[13]);


                                if($row[4] == 'larrieu.clement@gmail.com'){
                                    $user->setSuperAdmin(true);

                                    //$ldapService->addUserOnLDAP($user);
                                }

                                $userManager->updateUser($user);
                            }


                    }

                    $em->flush();
                }



            }

        }

        $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('ChouetteCoopAdminBundle:Admin:importMembres.html.twig',
                             array('form' => $form->createView(),
                                   'admin_pool' => $admin_pool,
                                   'path' => $path)
                            );

    }

    /**
     *
     * @param  Request $request
     * @return View
     */
    public function ldapMembresAction(Request $request)
    {

        $em = $this
            ->getDoctrine()
            ->getManager();

        $ldapService = $this->get('chouette.admin.ldap');

        $repositoryU = $em->getRepository('GlukoseUserBundle:User');

        $users = $repositoryU->findAll();

        foreach($users as $user){
            $user->setAccepteMail(true);
            $em->persist($user);
        }
        $em->flush();

        return $this->render('ChouetteCoopAdminBundle:Main:index.html.twig',
                             array());

    }


}
