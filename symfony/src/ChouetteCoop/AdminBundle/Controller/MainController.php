<?php

namespace ChouetteCoop\AdminBundle\Controller;

use Glukose\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('ChouetteCoopAdminBundle:Main:index.html.twig');
    }


    public function exportGHCodeBarreAction()
    {
        $listGh = array();
        $repositoryU = $this->getDoctrine()->getManager()->getRepository('GlukoseUserBundle:User');

        $users = $repositoryU->findBy(array('gh' => 1));
        /** @var User $gh */
        foreach ($users as $gh){
            $listGh []= $gh->getCodeBarre();
        }
        return new JsonResponse($listGh);
    }
}
