<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [ ]);
    }

    /**
     * @Route("/api/export/gh", name="chouette_coop_admin_export_gh")
     */
    public function exportGHCodeBarreAction()
    {
        $listGh = array();
        $repositoryU = $this->getDoctrine()->getManager()->getRepository('App:User');

        $users = $repositoryU->findBy(array('gh' => 1));
        /** @var User $gh */
        foreach ($users as $gh){
            $listGh []= $gh->getCodeBarre();
        }
        return new JsonResponse($listGh);
    }
}
