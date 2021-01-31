<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\Piaf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    const nbMois = 1;

    /**
     * @Route("/planning", name="planning")
     */
    public function index(): Response
    {
        return $this->render('planning/index.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }


    /**
     * TODO :
     * @Route("/routine", name="app_generate_creneaux_routine")
     * @return Response
     */
    public function generateCreneaux(EntityManagerInterface $em): Response
    {
        $creneauxRepository = $em->getRepository('App:Creneau');

        //Get all Creneaux actifs
        $creneauxGeneriques = $em->getRepository('App:CreneauGenerique')->findBy(['actif' => true]);

        //find date of the last creneau generated
        $lastCreneau = $creneauxRepository->findOneBy([], ['date' => 'DESC']);

        /** @var \DateTime $startDate */
        $startDate = $lastCreneau->getDate();
        $endDate = clone $startDate;
        $endDate->modify("+".self::nbMois." month");

        foreach ($creneauxGeneriques as $creneauGenerique){

            for($i=0; $i < 8; $i++){
                //find date for next occurence
                $nextDate = $this->nextOccurence($startDate->modify('+'.$i.' month'), $creneauGenerique->getFrequence(), $creneauGenerique->getJour());

                //Check if another creneau is not already generated for the same time, same dau
                //dump($creneauxRepository->findByCreneauGenerique($creneauGenerique->getId(), $nextDate, $creneauGenerique->getHeureDebut()));
                //if($creneauxRepository->findByCreneauGenerique($creneauGenerique->getId(), $nextDate, $creneauGenerique->getHeureDebut()) == null){

                $creneau = new Creneau();
                $creneau->setDate($nextDate);
                $creneau->setCreneauGenerique($creneauGenerique);
                $creneau->setHeureDebut($creneauGenerique->getHeureDebut());
                $creneau->setHeureFin($creneauGenerique->getHeureFin());
                $creneau->setTitre($creneauGenerique->getTitre());

                foreach ($creneauGenerique->getPostes() as $poste){
                    $piaf = new Piaf();
                    $piaf->setPiaffeur($poste->getReservationChouettos());
                    $piaf->setRole($poste->getRole());
                    $creneau->addPiaf($piaf);
                }

                $em->persist($creneau);
                //}
            }

        }
        $em->flush();


        //Create new Creneauz
        return $this->render('main/index.html.twig', []);
    }

    public function nextOccurence(\DateTimeInterface $date, $frequence, $jour){
        //Determine the week number
        $week = $date->format("W");

        $modulo = (int)$week % 4;
        $nextDate = clone $date;
        /** @var \DateTime $nextDate */
        $nextDate->modify('+'.$modulo.' week');
        $nextDate->modify('monday -1 week');
        $nextDate->modify('+'.$jour.' day');

        return $nextDate;
    }

}
