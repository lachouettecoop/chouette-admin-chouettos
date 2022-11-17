<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserAdminController extends CRUDController
{

    public function batchActionImprimeCarte(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();
        try {
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setCarteImprimee(true);
                $modelManager->update($selectedModel);
            }
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Une erreur est survenue');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', 'Impeccable ! T’es au top, merci…');

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }

    public function batchActionAnonymiser(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();
        try {
            foreach ($selectedModels as $selectedModel) {
                $this->anonymiserAction($selectedModel->getId());
            }
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Une erreur est survenue');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', 'Succès pour la RGPD ! T’es au top, merci…');

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }

    public function anonymiserAction($id)
    {
        $em = $this->getDoctrine()->getManager();


        $uuid = uniqid();
        $user = $this->admin->getObject($id);
        foreach ($user->getPersonneRattachee() as $personneRatachee) {
            $em->remove($personneRatachee);
        }
        foreach ($user->getAdresses() as $adresse) {
            $user->removeAdress($adresse);
        }

        $user->setEmail($uuid."@lachouettecoopRGPD.fr");
        $user->setNom("Nom_".$uuid);
        $user->setPrenom("Prénom_".$uuid);
        $user->setTelephone("");
        $user->setEnabled(false);
        $em->flush();

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );

    }
}