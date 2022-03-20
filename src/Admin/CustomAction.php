<?php
namespace App\Admin;

use App\Entity\CreneauGenerique;
use App\Entity\Poste;
use App\Entity\Piaf;
use App\Entity\Creneau;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManagerInterface;

class CustomAction extends CRUDController
{

    /**
     * @param $id
     */
    public function cloneAction($id)
    {
        /** @var CreneauGenerique $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $clonedObject = new CreneauGenerique();
        $clonedObject->setTitre($object->getTitre());
        $clonedObject->setFrequence($object->getFrequence());
        $clonedObject->setJour($object->getJour());
        $clonedObject->setHeureFin($object->getHeureFin());
        $clonedObject->setHeureDebut($object->getHeureDebut());

        $this->admin->create($clonedObject);

        foreach ($object->getPostes() as $poste){
            $posteNew = new Poste();
            $posteNew->setRole($poste->getRole());
            $posteNew->setCreneauGenerique($clonedObject);

            $this->admin->create($posteNew);
        }

        $this->addFlash('sonata_flash_success', 'L\'élément a correctement été dupliqué.');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     */
    public function generateAction($id)
    {
        /** @var CreneauGenerique $object */
        $creneauGenerique = $this->admin->getObject($id);
        $creneauxRepository = $this->getDoctrine()->getRepository('App:Creneau');
        $em = $this->getDoctrine()->getManager();

        if (!$creneauGenerique) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $now = new \DateTime("now");
        $endingDate = (new \DateTime("now"))->modify("+6 month");
        $nextDateCreneau = $this->nextOccurence($now, $creneauGenerique->getFrequence(), $creneauGenerique->getJour());
        while ($nextDateCreneau <= $endingDate) {
                if($creneauxRepository->findByCreneauGenerique($creneauGenerique->getId(), $nextDateCreneau, $creneauGenerique->getHeureDebut()) == null){
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

                    foreach ($creneauGenerique->getPostes() as $poste){
                        $piaf = new Piaf();
                        $piaf->setPiaffeur($poste->getReservationChouettos());
                        $piaf->setRole($poste->getRole());
                        $creneau->addPiaf($piaf);
                    }
                    $em->persist($creneau);
                }
                $nextDateCreneau = $this->nextOccurence($nextDateCreneau->modify("+4 week"), $creneauGenerique->getFrequence(), $creneauGenerique->getJour());
        }

        $em->flush();
        $this->addFlash('sonata_flash_success', 'Le créneau a correctement été généré.');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function nextOccurence(\DateTimeInterface $date, $frequency_creneau, $day_creneau){
        $week = (int)date_format($date, "W");
        // $day_creneau commence à zéro et format N à 1
        $day_week = (int)date_format($date, "N")-1;

        $frequency_week = (int)$week % 4;
        if ($frequency_week == 0) {
            $frequency_week = 4;
        }
        $ecart = 0;
        if ($frequency_week > $frequency_creneau) {
            $ecart = 4 - $frequency_week + $frequency_creneau;
        }
        elseif ($frequency_week < $frequency_creneau) {
            $ecart = $frequency_creneau - $frequency_week;
        }

        $nextDate = clone $date;
        /** @var \DateTime $nextDate */
        $nextDate->modify('+'.$ecart.' week');
        $nextDate->modify('+'.$day_creneau-$day_week.' day');

        return $nextDate;
    }
}
