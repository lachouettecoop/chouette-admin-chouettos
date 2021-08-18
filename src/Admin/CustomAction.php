<?php
namespace App\Admin;

use App\Entity\CreneauGenerique;
use App\Entity\Poste;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
}