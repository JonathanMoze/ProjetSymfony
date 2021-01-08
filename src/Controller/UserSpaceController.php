<?php

namespace App\Controller;

use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSpaceController extends AbstractController
{
    /**
     * @Route("/userSpace", name="user_space")
     */
    public function index(): Response
    {
        return $this->render('user_space/index.html.twig', [
            'controller_name' => 'UserSpaceController',
        ]);
    }


    /**
     * @Route("/user/suivreSerie/{id}", name="suivre_serie")
     */
    public function suivre_serie(Series $serie)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->addSeries($serie);

        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('home');
    }
}
