<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adhesion;
use App\Form\UserFirstType;
use App\Form\CreneauxType;
use App\Form\UserAdhesion;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\LdapController;


use DateTime;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/login_api", name="app_login_api")
     */
    public function loginAPI(LdapController $ldapController,
                             Request $request,
                             EntityManagerInterface $em,
                             GuardAuthenticatorHandler $guardHandler,
                             LoginFormAuthenticator $formAuthenticator): Response
    {
        $email = $request->get('username');
        $password = $request->get('password');

        $response = $ldapController->connectToLdapAsUser($email, $password);
        if($response){
            $user = $em->getRepository('App:User')->findOneByEmail($email);
            if($user){
                $user->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
                $em->persist($user);
                $em->flush();

                $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $formAuthenticator,
                    'main'
                );
                $responsejson = new JsonResponse(['etat' => 'success', 'userId' => $user->getId(), 'token' => $user->getApiToken()]);
                $responsejson->headers->set('Access-Control-Allow-Origin', '*');
                return $responsejson;
            }
        }
        $responsejson = new JsonResponse(['etat' => 'failure'], Response::HTTP_UNAUTHORIZED);
        $responsejson->headers->set('Access-Control-Allow-Origin', '*');
        return $responsejson;
    }

    /**
     * @Route("/login_admin_api", name="app_login_admin_api")
     */
    public function loginAdminAPI(LdapController $ldapController,
                             Request $request,
                             EntityManagerInterface $em,
                             GuardAuthenticatorHandler $guardHandler,
                             LoginFormAuthenticator $formAuthenticator): Response
    {
        $email = $request->get('username');
        $password = $request->get('password');

        $response = $ldapController->connectToLdapAsUser($email, $password);
        if($response){
            $user = $em->getRepository('App:User')->findOneByEmail($email);
            if($user && $user->getRoles() == 'ROLE_ADMIN'){
                $user->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
                $em->persist($user);
                $em->flush();

                $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $formAuthenticator,
                    'main'
                );
                $responsejson = new JsonResponse(['etat' => 'success', 'userId' => $user->getId(), 'token' => $user->getApiToken()]);
                $responsejson->headers->set('Access-Control-Allow-Origin', '*');
                return $responsejson;
            }
        }
        $responsejson = new JsonResponse(['etat' => 'failure'], Response::HTTP_UNAUTHORIZED);
        $responsejson->headers->set('Access-Control-Allow-Origin', '*');
        return $responsejson;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

     /**
     * @Route("/32assoadhesion", name="user_adhesion")
     */
    public function assoAdhesionForm(LdapController $ldapController,
    Request $request,
    EntityManagerInterface $em,
    GuardAuthenticatorHandler $guardHandler,
    LoginFormAuthenticator $formAuthenticator): Response
    {
        $form = $this->createForm(UserAdhesion::class);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('csvFile')->getData();

            if ($file) {
                // Handle CSV processing
                if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                    // Skip the header row if your CSV has headers
                    fgetcsv($handle);
                    
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $email = $data[0];
                        $dateAdhesion = $data[1];
                        $annee = $data[2];
                        $montant = $data[3];
                        $modePaiement = $data[4];
                        
                        $dateObject = \DateTime::createFromFormat('d/m/Y', $dateAdhesion);

                        if (!$dateObject) {
                            return new Response('wrong date format : '.$dateAdhesion.' for email : '.$email, 500);
                        }

                        if ($dateObject && $dateObject->format('d/m/Y') !== $dateAdhesion) {
                            return new Response('wrong date format : '.$dateAdhesion.' for email : '.$email, 500);
                        }

                        $allowedValues = ['helloAsso', 'cheque', 'especes', 'cb', 'virement', 'solViolette', 'autre'];

                        if (!in_array($modePaiement, $allowedValues)) {
                            return new Response('Failed to find mode de paiement with key : '.$modePaiement.' for email : '.$email, 500);
                        }
                        
                        // Find the user by email
                        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

                        foreach ($user->getAdhesions() as $adhesion) {
                            if ($adhesion->getAnnee() == $annee) {
                            return new Response('An adhesion already exists for annee : '.$annee.' for email : '.$email, 500);
                            }
                        }

                        if ($user) {
                            // Create a new Adhesion entity and set its properties
                            $adhesion = new Adhesion();
                            $adhesion->setUser($user);
                            $adhesion->setMontant($montant);
                            $adhesion->setModePaiement($modePaiement);
                            $adhesion->setAnnee($annee);
                            $adhesion->setDateAdhesion($dateObject);
                            
                            // Persist the new Adhesion entity
                            $em->persist($adhesion);
                        } else {
                            return new Response('Failed to find user with email : '.$email, 500);
                        }
                    }
                    fclose($handle);
                    
                    // Flush once after processing all rows to save everything at once
                    $em->flush();
                    
                    // Optional: Add a success message or redirect

                    return $this->render('security/adhesion_final.html.twig');
                } else {
                    // Handle file open error
                    return new Response('Failed to open the uploaded CSV file.', 500);
                }
            }
           
        }

        return $this->render('security/adhesion.html.twig', ['form' => $form->createView()] );

    }
    
    /**
     * @Route("/31generercreneaux", name="app_generer_creneaux")
     */
    public function genererCreneaux(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(CreneauxType::class);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            $pattern = $data['pattern'];

            $creneauxRepository = $em->getRepository('App:Creneau');
            $creneauxGeneriques = $em->getRepository('App:CreneauGenerique')->findBy(['actif' => true, 'frequence' => $pattern]);
            $increment = ($pattern === 3) ? 28 : 1;

            foreach ($creneauxGeneriques as $creneauGenerique){
                PlanningController::createCreneauxFromCreneauGenerique(
                    $creneauGenerique,
                    $creneauxRepository,
                    $em,
                    $startDate,
                    $endDate,
                    $increment
                );
            }
            $em->flush();
            return $this->render('security/generer_creneaux_final.html.twig');
        }

        return $this->render('security/generer_creneaux.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/31enregistrement", name="app_premier_enregistrement")
     */
    public function premierEnregistrement(Request $request,
                                          EntityManagerInterface $em,
                                          MailerInterface $mailer,
                                          UserPasswordEncoderInterface $passwordEncoder,
                                          LdapController $ldapController): Response
    {
        $user = new User();
        $form = $this->createForm(UserFirstType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //check si l'email n'est pas en base
            $oldUser = $em->getRepository("App:User")->findOneBy(['email' => $user->getEmail()]);
            if($oldUser){
                $this->addFlash('warning', "Cette adresse email existe déjà en base !");
            } else {

                //encodage mdp + génération codebarre
                $user->setPassword($passwordEncoder->encodePassword($user, $user->getMotDePasse()));
                $timestamp = time();
                if (empty($user->getCodeBarre())) {
                    $codeBarre = $this->generateEAN($timestamp);
                    $user->setCodeBarre($codeBarre);
                }


                $dateDebut = (new \DateTime("now"));

                //enregistrement pour avoir l'ID de l'objet
                $user->setEnabled(true);
                $user->setDateDebutPiaf($dateDebut);
                $em->persist($user);

                if ($user->getPeriodeEssai()) {
                    $user->setwasInPeriodeEssai(true);
                }

                $em->flush();

                //on ajoute au LDAP et on efface le mdp
                $ldapController->addUserOnLDAP($user);
                $user->setMotDePasse("");
                $em->persist($user);
                $em->flush();

                $lastId = $user->getId();
                $connection = $em->getConnection();

                // Ajout du rôle Chouettos
                $sql = "INSERT INTO user_role (user_id, role_id) VALUES (:lastId, 2);";
                $stmt = $connection->prepare($sql);
                $stmt->executeQuery(["lastId" => $lastId]);
                // Ajout du rôle Chouettos
                $sql1 = "INSERT INTO user_role (user_id, role_id) VALUES (:lastId, 11);";
                $stmt1 = $connection->prepare($sql1);
                $stmt1->executeQuery(["lastId" => $lastId]);

                $essai = $user->getPeriodeEssai();

                if ($essai) {
                    $message = (new Email())
                    ->subject('Bienvenue à la Chouette Coop - La Chouette Coop')
                    ->from('bureau-des-membres@lachouettecoop.fr')
                    ->to($user->getEmail())
                    ->html(
                        $this->renderView('planning/notificationEssai.html.twig', ['essai' => $essai]),
                        'text/html'
                    );
                    $mailer->send($message);
                }

                return $this->render('security/premier_enregistrement_final.html.twig');
            }
        }

        return $this->render('security/premier_enregistrement.html.twig', ['form' => $form->createView()] );

    }

    private function generateEAN($number)
    {
        $code = '24' . $number;
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
        // loop backwards to make the loop length-agnostic. The same basic functionality
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }


    /**
     * @Route("/resetting/request", name="fos_user_resetting_request")
     */
    public function forgottenPassword(Request $request,
                                      UserPasswordEncoderInterface $encoder,
                                      MailerInterface $mailer,
                                      TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('fos_user_resetting_request');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setConfirmationToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('main');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new Email())
                ->subject('Votre mot de passe - La Chouette Coop')
                ->from('bureau-des-membres@lachouettecoop.fr')
                ->to($user->getEmail())
                ->html(
                    $this->renderView('security/resetting-email.html.twig', ['confirmationUrl' => $url, 'user' =>$user]),
                    'text/html'
                );

            $mailer->send($message);
            $this->addFlash('info', 'Mail envoyé');
        }
        return $this->render('security/forgotten_password.html.twig');
    }


    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, LdapController $ldapController)
    {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('fos_user_resetting_request');
            }

            if($request->request->get('password') != $request->request->get('password2')){
                $this->addFlash('danger', 'Les deux mots de passe ne correspondent pas');
            } else {
                $user->setConfirmationToken(null);
                $user->setMotDePasse($request->request->get('password'));
                $ldapController->updateUserPassOnLDAP($user);


                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

                $user->setMotDePasse("");
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('info', 'Mot de passe mis à jour, vous pouvez vous connecter !');

                return $this->redirectToRoute('main');
            }
        }
        return $this->render('security/resetting_password.html.twig', ['token' => $token]);

    }
}
