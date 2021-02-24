<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\CreneauGenerique;
use App\Entity\Piaf;
use App\Entity\Poste;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    const nbMois = 1;

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

}
