<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
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
                $entityManager->flush();

                $this->addFlash('info', 'Mot de passe mis à jour, vous pouvez vous connecter !');

                return $this->redirectToRoute('main');
            }
        }
        return $this->render('security/resetting_password.html.twig', ['token' => $token]);

    }
}
