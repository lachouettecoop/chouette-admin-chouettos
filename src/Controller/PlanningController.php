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
    const nbMois = 1;

    /**
     * @Route("/mouli/4dzq564d6/piaf", name="app_cron_compteur_piaf")
     * @return Response
     */
    public function compterPiafEffectuees(EntityManagerInterface $em): Response
    {
        $dateDebut = (new \DateTime("now"))->modify("midnight +1 hour");
        $dateFin = (new \DateTime("now"));

        $crenaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);

        /** @var Creneau $crenau */
        foreach ($crenaux as $crenau) {
            foreach ($crenau->getPiafs() as $piaf) {
                /** @var User $piaffeur */
                $piaffeur = $piaf->getPiaffeur();
                if ($piaffeur != null and $piaf->getPourvu() and !$piaf->getComptabilise()) {
                    $piaffeur->setNbPiafEffectuees($piaffeur->getNbPiafEffectuees() +1);
                    $piaffeur->setStatut($this->calculStatus($piaffeur));
                    $piaf->setComptabilise(true);

                    //compta nbPiafGH
                    if($piaf->getRole()->getId() == 1){
                        $piaffeur->setNbPiafGH($piaffeur->getNbPiafGH() +1);
                        if($piaffeur->getNbPiafGH() == 10){
                            $piaffeur->addRolesChouette($em->getRepository('App:Role')->find(8));
                        }
                    } elseif($piaf->getRole()->getId() == 3){
                        $piaffeur->setNbPiafCaisse($piaffeur->getNbPiafCaisse() +1);
                        if($piaffeur->getNbPiafCaisse() == 10){
                            $piaffeur->addRolesChouette($em->getRepository('App:Role')->find(7));
                        }
                    }
                    $em->persist($piaffeur);
                    $em->persist($piaf);
                }
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
            if ($user->getAbsenceLongueDureeCourses()) {
                $user->setNbPiafEffectuees($user->getNbPiafEffectuees() +1);
            }
            if(!$user->getAbsenceLongueDureeSansCourses()){
                $user->setNbPiafAttendues($user->getNbPiafAttendues() +1);
            }
            $user->setStatut($this->calculStatus($user));
            $em->persist($user);
        }
        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    public function calculStatus(User $user){
        $attendues = $user->getNbPiafAttendues();
        $effectues = $user->getNbPiafEffectuees();

        if ($effectues >= $attendues){
            $status = 'très chouette';
        } elseif ($effectues >= ($attendues - 2)){
            $status = 'chouette';
        } elseif ($effectues < ($attendues - 2)){
            $status = 'chouette en alerte';
        }

        return $status;
    }

    /**
     *
     * @Route("/mouli/4dzq564d6/init", name="app_plan_moil_init")
     * @return Response
     */
    public function init(EntityManagerInterface $em): Response
    {
        $dateBascule = new \DateTime();
        $dateBascule->setDate(2020, 10, 11);
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
        $dateDebut = (new \DateTime("now"))->modify("+5 days");
        $dateFin = (new \DateTime("now"))->modify("+6 days");
        $crenaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);

        $piafs = [];
        /** @var Creneau $crenau **/
        foreach ($crenaux as $crenau){
            foreach ($crenau->getPiafs() as $piaf){
                if($piaf->getPiaffeur() != null){
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

        $emailContent = $this->renderView('planning/messageGenerique.html.twig', ['sujet' => $sujet, 'corps' => $corps]);
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
        $crenaux = $em->getRepository('App:Creneau')->findCreneauByDate($dateDebut, $dateFin);

        $users = [];
        /** @var Creneau $crenau */
        foreach ($crenaux as $crenau){
            foreach ($crenau->getPiafs() as $piaf){
                if($piaf->getPiaffeur() == null){
                    foreach ($crenau->getCreneauGenerique()->getReserves() as $reserve){
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
     * @Route("/routine", name="app_generate_creneaux_routine")
     * @return Response
     */
    public function generateCreneaux(EntityManagerInterface $em): Response
    {
        $creneauxRepository = $em->getRepository('App:Creneau');

        //Get all Creneaux actifs
        $creneauxGeneriques = $em->getRepository('App:CreneauGenerique')->findBy(['actif' => true]);

        /*for($i=1; $i < 5; $i++){
            for($j=0; $j < 6; $j++){
                $creneauGenerique = new CreneauGenerique();
                $creneauGenerique->setTitre("Ouverture Mag");
                $creneauGenerique->setJour($j);
                $creneauGenerique->setFrequence($i);
                $creneauGenerique->setHeureDebut(new \DateTime('1970-01-01 14:30:00'));
                $creneauGenerique->setHeureFin(new \DateTime('1970-01-01 17:30:00'));

                for($k=0; $k < 6; $k++){

                    $poste = new Poste();
                    $poste->setRole($em->getRepository('App:Role')->find(rand (1 ,3)));
                    $poste->setReservationChouettos(null);
                    $creneauGenerique->addPoste($poste);
                }
                $em->persist($creneauGenerique);
                $creneauGenerique = new CreneauGenerique();
                $creneauGenerique->setTitre("Fermeture Mag");
                $creneauGenerique->setJour($j);
                $creneauGenerique->setFrequence($i);
                $creneauGenerique->setHeureDebut(new \DateTime('1970-01-01 17:00:00'));
                $creneauGenerique->setHeureFin(new \DateTime('1970-01-01 20:00:00'));
                for($k=0; $k < 6; $k++){
                    $poste = new Poste();
                    $poste->setRole($em->getRepository('App:Role')->find(rand (1 ,3)));
                    $poste->setReservationChouettos(null);
                    $creneauGenerique->addPoste($poste);
                }
                $em->persist($creneauGenerique);
            }
        }*/
        //find date of the last creneau generated
        $lastCreneau = $creneauxRepository->findOneBy([], ['fin' => 'DESC']);

        /** @var \DateTime $startDate */
        $startDate = $lastCreneau->getDebut();
        $endDate = clone $startDate;
        $endDate->modify("+".(self::nbMois*4)." week");

        foreach ($creneauxGeneriques as $creneauGenerique){

            for($i=0; $i < 13; $i++){
                //find date for next occurence
                $startDateLocal = clone $startDate;

                $nextDate = $this->nextOccurence($startDateLocal->modify('+'.($i*4).' week'), $creneauGenerique->getFrequence(), $creneauGenerique->getJour());
                //Check if another creneau is not already generated for the same time, same dau

                if($creneauxRepository->findByCreneauGenerique($creneauGenerique->getId(), $nextDate, $creneauGenerique->getHeureDebut()) == null){

                    $creneau = new Creneau();
                    //$creneau->setDate($nextDate);
                    $creneau->setCreneauGenerique($creneauGenerique);

                    $nextDateFin = clone $nextDate;
                    $nextDate->setTime($creneauGenerique->getHeureDebut()->format('H'), $creneauGenerique->getHeureDebut()->format('i'), $creneauGenerique->getHeureDebut()->format('s'));
                    $nextDateFin->setTime($creneauGenerique->getHeureFin()->format('H'), $creneauGenerique->getHeureFin()->format('i'), $creneauGenerique->getHeureFin()->format('s'));
                    $creneau->setDebut($nextDate);
                    $creneau->setFin($nextDateFin);
                    $creneau->setTitre($creneauGenerique->getTitre());

                    foreach ($creneauGenerique->getPostes() as $poste){
                        $piaf = new Piaf();
                        $piaf->setPiaffeur($poste->getReservationChouettos());
                        $piaf->setRole($poste->getRole());
                        $creneau->addPiaf($piaf);
                    }


                    $em->persist($creneau);
                }

            }

        }
        $em->flush();

        return $this->render('main/index.html.twig', []);
    }

    /**
     * Determine the next occurence of a creneau given its frequency and day of the week
     *
     * @param \DateTimeInterface $date
     * @param $frequence
     * @param $jour
     *
     * @return \DateTime|\DateTimeInterface
     */
    public function nextOccurence(\DateTimeInterface $date, $frequence, $jour){
        //Determine the week number
        $week = $date->format("W");

        $modulo = (int)$week % 4;
        $ecart = (4 - $modulo) + $frequence;
        $nextDate = clone $date;
        /** @var \DateTime $nextDate */
        $nextDate->modify('+'.$ecart.' week');
        $nextDate->modify('monday -1 week');
        $nextDate->modify('+'.$jour.' day');

        return $nextDate;
    }

    public function sendEmail($sujet, $email, $content, MailerInterface $mailer)
    {
        $message = (new Email())
            ->subject($sujet)
            ->from('bureau-des-membres@lachouettecoop.fr')
            ->to($email)
            ->html($content,'text/html')
        ;

        $mailer->send($message);

        return true;
    }

}
