<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\CreneauGenerique;
use App\Entity\Paiement;
use App\Entity\Piaf;
use App\Entity\Poste;
use App\Entity\Reserve;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    /**
     * @Route("/mouli/4dzq564d6/piaf", name="app_cron_compteur_piaf")
     * @return Response
     */
    public function compterPiafEffectuees(EntityManagerInterface $em): Response
    {
        $piafs = $em->getRepository('App:Piaf')->findPIAFaComptabiliser();

        foreach ($piafs as $piaf) {
            /** @var User $piaffeur */
            $piaffeur = $piaf->getPiaffeur();
            $creneau = $piaf->getCreneau();
            $isDemiPiaf = $creneau->getDemiPiaf();
            $isDemiPiafFull = false;

            if ($isDemiPiaf) {
                $nbDemiPiaf = $piaffeur->getNbDemiPiaf();
                if ($nbDemiPiaf == 0) {
                    $piaffeur->setNbDemiPiaf(1);
                    $piaf->setComptabilise(true);
                }
                if ($nbDemiPiaf == 1) {
                    $piaffeur->setNbDemiPiaf(0);
                    $isDemiPiafFull = true;
                }
            }
            if ((!$isDemiPiaf or $isDemiPiafFull) and $piaffeur != null and $piaf->getPourvu() and !$piaf->getComptabilise()) {
                $piaffeur->setNbPiafEffectuees($piaffeur->getNbPiafEffectuees() +1);
                $piaf->setComptabilise(true);

                //compta nbPiafGH
                if($piaf->getRole()->getId() == 1){
                    $piaffeur->setNbPiafGH($piaffeur->getNbPiafGH() +1);
                } elseif($piaf->getRole()->getId() == 3){
                    $piaffeur->setNbPiafCaisse($piaffeur->getNbPiafCaisse() +1);
                }
                $em->persist($piaffeur);
                $em->persist($piaf);
            }
        }
        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     * @Route("/mouli/4dzq5848az/piafattendues", name="app_cron_compteur_piaf_attendues")
     * @return Response
     */
    public function compterPiafAttendues(EntityManagerInterface $em): Response
    {
        //a lancer le soir à 23h
        $dateDebut = (new \DateTime("now"));

        $users = $em->getRepository('App:User')->findByDateDebutPiaf($dateDebut);
        /** @var User $user */
        foreach ($users as $user) {
            if ($user->getAbsenceLongueDureeCourses() or $user->getDispenseDefinitive()) {
                $user->setNbPiafEffectuees($user->getNbPiafEffectuees() +1);
            }
            if(!$user->getAbsenceLongueDureeSansCourses()){
                $user->setNbPiafAttendues($user->getNbPiafAttendues() +1);
            }
            $em->persist($user);
        }
        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/notif/warningchouettos", name="app_cron_notif_warning")
     * @return Response
     */
    public function warningChouettos(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository('App:User')->findForWarningMail();

        foreach ($users as $user) {
            if(filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)){
                $emailContent = $this->renderView('planning/notificationWarning.html.twig', []);
                $this->sendEmail('Chouettos en alerte', $user->getEmail(), $emailContent, $mailer);
            }
        }

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/notif/reminderAbsenceLongueDuree", name="app_cron_notif_reminder_absence")
     * @return Response
     */
    public function reminderAbsenceLongueDuree(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository('App:User')->findForAbsenceLongueDureeCourses();

        foreach ($users as $user) {
            if(filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)){
                $emailContent = $this->renderView('planning/notificationAbsenceLongueDuree.html.twig', []);
                $this->sendEmail("Rappel concernant l'absence longue durée", $user->getEmail(), $emailContent, $mailer);
            }
        }

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/mouli/status", name="app_cron_update_status")
     * @return Response
     */
    public function updateStatus(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository('App:User')->findAll();
        foreach ($users as $user) {
            $attendues = $user->getNbPiafAttendues();
            $effectues = $user->getNbPiafEffectuees();

            if ($effectues >= $attendues) {
                $status = 'très chouette';
            } elseif ($effectues >= ($attendues - 2)) {
                $status = 'chouette';
            } elseif ($effectues < ($attendues - 2)) {
                $status = 'chouette en alerte';
            }

            $user->setStatut($status);
            $em->persist($user);
        }

        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/mouli/essai", name="app_cron_update_periode_essai")
     * @return Response
     */
    public function updatePeriodeEssai(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $users = $em->getRepository('App:User')->findForPeriodeEssai();
        $date = date_create();
        $nextWeekDate = date("Y-m-d", strtotime("7 days"));
        $next3WeeksDate = date("Y-m-d", strtotime("21 days"));
        
        foreach ($users as $user) {
            $essai = $user->getPeriodeEssai();
            
            if ( date_format($essai,"Y-m-d") == $next3WeeksDate) {
                $emailContent = $this->renderView('planning/notificationEssaiFirstReminder.html.twig', []);
                $this->sendEmail("Informations relative à votre période d'essai", $user->getEmail(), $emailContent, $mailer);
            }
            if ( date_format($essai,"Y-m-d") == $nextWeekDate) {
                $emailContent = $this->renderView('planning/notificationEssaiSecondReminder.html.twig', []);
                $this->sendEmail("Rappel suite de votre période d'essai", $user->getEmail(), $emailContent, $mailer);
            }

            if ($date > $essai && $user->getEnabled()) {
                $user->setEnabled(false);
                $em->persist($user);

                $emailContent = $this->renderView('planning/notificationEssaiNotInterested.html.twig', []);
                $this->sendEmail("Informations de votre fin de période d'essai", $user->getEmail(), $emailContent, $mailer);
            }
        }

        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/mouli/4dzq564d6/init", name="app_plan_moil_init")
     * @return Response
     */
    public function init(EntityManagerInterface $em): Response
    {
        $dateBascule = new \DateTime();
        $dateBascule->setDate(2021, 11, 29);
        $users = $em->getRepository('App:User')->findAll();

        foreach ($users as $user){
            /** @var Paiement $paiment */
            $paiment = $user->getPaiements()->first();
            if($paiment){
                $user->setStatut('très chouette');
                if($paiment->getDateEcheance() > $dateBascule){
                    $user->setDateDebutPiaf($paiment->getDateEcheance());
                } else {
                    $user->setDateDebutPiaf($dateBascule);
                }
            } else {
                $user->setStatut('chouette en alerte');
            }

            $em->persist($user);
        }
        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/notif/4dzq589az6/participation", name="app_notification_participation")
     * @return Response
     */
    public function notificationParticipation(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $dateDebut = (new \DateTime("now"))->modify("+3 days");
        $dateFin = (new \DateTime("now"))->modify("+4 days");
        $creneaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);

        $piafs = [];
        /** @var Creneau $creneau **/
        foreach ($creneaux as $creneau){
            foreach ($creneau->getPiafs() as $piaf){
                if($piaf->getPiaffeur() != null and $piaf->getStatut() != "remplacement"){
                    $piafs[] = $piaf;
                }
            }
        }

        foreach ($piafs as $piaf){
            if(filter_var($piaf->getPiaffeur()->getEmail(), FILTER_VALIDATE_EMAIL)){
                $emailContent = $this->renderView('planning/notificationPiafApproche.html.twig', ['piaf' => $piaf]);
                $this->sendEmail('Votre PIAF approche - La Chouette Coop', $piaf->getPiaffeur()->getEmail(), $emailContent, $mailer);
            }
        }

        return $this->render('main/index.html.twig', []);
    }

    /**
     * @Route("/dist_api/send_email", name="app_send_mail", methods={"POST"})
     * @return Response
     */
    public function apiSendEmail(Request $request, MailerInterface $mailer): Response
    {
        $sujet = $request->get('sujet');
        $email = $request->get('email');
        $corps = $request->get('corps');
        $isRaw = $request->get('isRaw');

        if (filter_var($isRaw, \FILTER_VALIDATE_BOOLEAN)) {
        $emailContent = $this->renderView('template_email_raw.html.twig', ['corps' => $corps]);
        } else {
            $emailContent = $this->renderView('planning/messageGenerique.html.twig', ['sujet' => $sujet, 'corps' => $corps]);
        }

        $this->sendEmail($sujet.'- La Chouette Coop', $email, $emailContent, $mailer);

        $responsejson = new JsonResponse(['etat' => 'success']);
        $responsejson->headers->set('Access-Control-Allow-Origin', '*');
        return $responsejson;
    }

    /**
     *
     * @Route("/notif/4dzq564d6/reserve", name="app_notification_reserve")
     * @return Response
     */
    public function notificationReserve(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $dateDebut = (new \DateTime("now"));
        $dateFin = (new \DateTime("now"))->modify("+2 days");
        $creneaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);

        $users = [];
        /** @var Creneau $creneau */
        foreach ($creneaux as $creneau){
            foreach ($creneau->getPiafs() as $piaf){
                if($piaf->getPiaffeur() == null){
                    foreach ($creneau->getCreneauGenerique()->getReserves() as $reserve){
                        if($reserve->getUser()->getRolesChouette()->contains($piaf->getRole())){
                            $users[$reserve->getUser()->getEmail()][] = $em->getRepository('App:Piaf')->find($piaf->getId());
                        }
                    }
                }
            }
        }

        foreach ($users as $email => $piafs){
            $emailContent = $this->renderView('planning/notificationReserve.html.twig', ['piafs' => $piafs]);
            $this->sendEmail('Réserve - La Chouette Coop', $email, $emailContent, $mailer);
        }

        return $this->render('main/index.html.twig', []);
    }

    /**
     *
     * @Route("/notif/4dzad554d6/routine", name="app_generate_creneaux_routine")
     * @return Response
     */
    public function generateCreneaux(EntityManagerInterface $em): Response
    {
        $creneauxRepository = $em->getRepository('App:Creneau');
        $fourMonthsLater = (new \DateTime())->modify('+4 months');
        
        // frequence 1 (type A) = Modele principal, doit prendre en compte le modèle vacances (fréquence 2 - type B)
        $creneauxGeneriquesA = $em->getRepository('App:CreneauGenerique')->findBy(['actif' => true, 'frequence' => 1]);
        $lastCreneauA = $creneauxRepository->createQueryBuilder('c')
        ->innerJoin('c.creneauGenerique', 'cg')
        ->where('cg.frequence = :frequence')
        ->orWhere('cg.frequence = :frequence2')
        ->andWhere('c.fin <= :fourMonthsLater')
        ->setParameter('frequence', 1)
        ->setParameter('frequence2', 2)
        ->setParameter('fourMonthsLater', $fourMonthsLater)
        ->orderBy('c.fin', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
        PlanningController::generateCreneauxAnyType($em, $creneauxRepository, $creneauxGeneriquesA, $lastCreneauA, 1);
        // frequence 3 (type C) = 1 semaine sur 4
        $creneauxGeneriquesC = $em->getRepository('App:CreneauGenerique')->findBy(['actif' => true, 'frequence' => 3]);
        $lastCreneauC = $creneauxRepository->createQueryBuilder('c')
        ->innerJoin('c.creneauGenerique', 'cg')
        ->where('cg.frequence = :frequence')
        ->andWhere('c.fin <= :fourMonthsLater')
        ->setParameter('frequence', 3)
        ->setParameter('fourMonthsLater', $fourMonthsLater)
        ->orderBy('c.fin', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
        PlanningController::generateCreneauxAnyType($em, $creneauxRepository, $creneauxGeneriquesC, $lastCreneauC, 27);


        return $this->render('main/index.html.twig', []);
    }

    /**
     * Send mail to Équipe planning for a recapitulative
     *
     * @Route("/notif/4dzadui4d6/recap", name="app_send_recap")
     * @return Response
     *
     */
    public function sendCreneauRecap(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $dateDebut = (new \DateTime("now"));
        $dateFin = (new \DateTime("now"))->modify("+7 days");
        $creneaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);
        usort($creneaux, function($a, $b) {
            return ($a->getDebut() < $b->getDebut()) ? -1 : 1;
        });

        $emailContent = $this->renderView('planning/recapCreneaux.html.twig', ['creneaux' => $creneaux, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        $this->sendEmail('Récapitulatif des créneaux pour les 7 jours à venir', 'bureau-des-membres@lachouettecoop.fr', $emailContent, $mailer);

        return $this->render('main/index.html.twig', []);
    }

    public function sendEmail($sujet, $email, $content, MailerInterface $mailer, $cc = "")
    {
        $message = (new Email())
            ->subject($sujet)
            ->from('bureau-des-membres@lachouettecoop.fr')
            ->to($email)
            ->html($content,'utf-8');

        if ($cc) {
            $message->cc($cc);
        }

        $mailer->send($message);

        return true;
    }

    static function generateCreneauxAnyType(EntityManagerInterface $em, $creneauxRepository, $creneauxGeneriques, $lastCreneau, $increment)
    {
        $inFourMonths = new \DateTime();
        $inFourMonths->modify("+4 month")->modify('last day of this month');  // Get the date in 4 months

        if(!$lastCreneau){
            $lastCreneau = new Creneau();
            $lastCreneau->setDebut(new \DateTime());
        } else {
            $startingDate = $lastCreneau->getDebut()->modify("+1 day");
        }

        if ($startingDate < $inFourMonths) {
            $endingDate = clone $inFourMonths;

            foreach ($creneauxGeneriques as $creneauGenerique){
  
              PlanningController::createCreneauxFromCreneauGenerique(
                  $creneauGenerique,
                  $creneauxRepository,
                  $em,
                  $startingDate,
                  $endingDate,
                  $increment
              );
          }
          $em->flush();

        }
    }

    public static function createCreneauxFromCreneauGenerique($creneauGenerique, $repository, $entityManager, $startingDate, $endingDate, $dayIncrement) {
            $nextDateCreneau = clone $startingDate;
            while ($nextDateCreneau <= $endingDate) {
                if($creneauGenerique->getJour() == ($nextDateCreneau->format('N') - 1) && $repository->findByCreneauGenerique($creneauGenerique->getId(), $nextDateCreneau, $creneauGenerique->getHeureDebut()) == null){
                    $creneau = new Creneau();
                    $creneau->setCreneauGenerique($creneauGenerique);
                    $creneau->setHorsMag($creneauGenerique->getHorsMag());

                    $nextDateDebutCreneau = clone $nextDateCreneau;
                    $nextDateFinCreneau = clone $nextDateCreneau;
                    $nextDateDebutCreneau->setTime($creneauGenerique->getHeureDebut()->format('H'), $creneauGenerique->getHeureDebut()->format('i'), $creneauGenerique->getHeureDebut()->format('s'));
                    $nextDateFinCreneau->setTime($creneauGenerique->getHeureFin()->format('H'), $creneauGenerique->getHeureFin()->format('i'), $creneauGenerique->getHeureFin()->format('s'));
                    $creneau->setDebut($nextDateDebutCreneau);
                    $creneau->setFin($nextDateFinCreneau);
                    $creneau->setTitre($creneauGenerique->getTitre());
                    $creneau->setDemiPiaf($creneauGenerique->getDemiPiaf());

                    foreach ($creneauGenerique->getPostes() as $poste){
                        $piaf = new Piaf();
                        $piaf->setPiaffeur($poste->getReservationChouettos());
                        $piaf->setRole($poste->getRole());
                        $piaf->setDescription($poste->getDescription());
                        $creneau->addPiaf($piaf);
                    }

                    $entityManager->persist($creneau);
                }
                $nextDateCreneau = $nextDateCreneau->modify("+$dayIncrement day");
            }
    }
}
