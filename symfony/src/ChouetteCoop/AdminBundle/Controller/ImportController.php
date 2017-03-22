<?php

namespace ChouetteCoop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Glukose\UserBundle\Entity\User;
use Glukose\ContactBundle\Entity\Adresse;
use Glukose\UserBundle\Entity\Adhesion;


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
   * Create Barcodes for users
   *
   * @param  Request $request
   * @return View
   */
  public function creationDeCodeBarreAction(Request $request)
  {
        $em = $this
          ->getDoctrine()
          ->getManager();

        $userRep = $em->getRepository('GlukoseUserBundle:User');

        $users = $userRep->findAll();

        $timestamp = time();

        $i = 0;
        foreach ($users as $user) {
            $i++;
            $codeBarre = $this->generateEAN(($timestamp + $i));
            $user->setCodeBarre($codeBarre);
            $em->persist($user);
        }

        $em->flush();
        $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('ChouetteCoopAdminBundle:Main:index.html.twig',
                             array('admin_pool' => $admin_pool)
                            );
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

                            if(filter_var($row[8], FILTER_VALIDATE_EMAIL) != false){
                                $userOld = $repositoryU->findOneBy(array('email' => $row[8]));

                                if(!$userOld){
                                  $user = $userManager->createUser();

                                  $user->setUsername($row[8]);
                                  $user->setEmail($row[8]);
                                  $user->setNom($row[1]);
                                  $user->setPrenom($row[2]);
                                  $user->setTelephone($row[7]);
                                  $user->setPlainPassword('1234'.time());
                                  $user->setMotDePasse('1234'.time());

                                  $user->setCodeBarre($row[5]);
                                  $user->setEnabled(false);
                                  if($row[9] == '1'){
                                      $user->setEnabled(true);
                                  }
                                  if($row[10] == '1'){
                                      $user->setAccepteMail(true);
                                  }

                                  $user->setDateNaissance($this->dateToSQL($row[3], 'D, d M Y H:i:s O'));
                                  $user->setDomaineCompetence($row[11]);

                                  if($row[4] == 'larrieu.clement@gmail.com'){
                                      $user->setSuperAdmin(true);

                                      //$ldapService->addUserOnLDAP($user);
                                  }

                                  $adresse = new Adresse();
                                  $adresse->setLigne1($row[13]);
                                  $adresse->setLigne2($row[14]);
                                  $adresse->setLigne3($row[15]);
                                  $adresse->setCodePostal($row[16]);
                                  $adresse->setVille($row[17]);

                                  $user->addAdress($adresse);
                                  $em->persist($adresse);

                                  $tabAdhesionAnnee = explode(',', $row[20]);
                                  $tabAdhesionDate = explode(',', $row[21]);
                                  $tabAdhesionMontant = explode(',', $row[22]);
                                  $i = 0;
                                  if($tabAdhesionAnnee){
                                    foreach ($tabAdhesionAnnee as $value) {
                                      if(isset($tabAdhesionAnnee[$i]) && $tabAdhesionAnnee[$i] !=''  && isset($tabAdhesionDate[$i]) && $tabAdhesionDate[$i]!= '' && isset($tabAdhesionMontant[$i]) && $tabAdhesionMontant[$i] !='' ){
                                        $adhesion = new Adhesion();
                                        if(is_int($tabAdhesionAnnee[$i]) ){
                                        $adhesion->setAnnee($tabAdhesionAnnee[$i]);
                                        $adhesion->setDateAdhesion($this->dateToSQL($tabAdhesionDate[$i], 'd/m/Y'));
                                        $adhesion->setMontant($tabAdhesionMontant[$i]);
                                        $i++;
                                        $adhesion->setUser($user);
                                        $persist($adhesion);
                                        }
                                      }
                                    }
                                  }

                                  $userManager->updateUser($user);
                              }

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

    function dateToSQL($frenchDate, $format) {
      $date = \DateTime::createFromFormat($format, $frenchDate);
      return $date ? $date : null;
    }


}
