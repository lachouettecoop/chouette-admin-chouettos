<?php
namespace App\Admin;

use App\Entity\CreneauGenerique;
use App\Entity\Poste;
use App\Entity\Piaf;
use App\Entity\Creneau;
use App\Controller\PlanningController;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception;

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

        if ($creneauGenerique->getFrequence() == 3) {
            $increment = 28;
        } else {
            $increment = 1;
        }

        $now = new \DateTime("now");
        $endingDate = (new \DateTime("now"))->modify("+3 month");

        try {
            PlanningController::createCreneauxFromCreneauGenerique(
                $creneauGenerique,
                $creneauxRepository,
                $em,
                $now,
                $endingDate,
                $increment);

            $em->flush();
            $this->addFlash('sonata_flash_success', 'Le créneau a correctement été généré.');

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'La génération du créneau a rencontré une erreur.');
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
