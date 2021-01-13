<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rating;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/moderation", name="moderation")
     */
    public function moderation()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();


        if($user == "anon." || !$user->getAdmin()){
            return $this->redirectToRoute('app_login');
        }  

        $ratings = $this->getDoctrine()
        ->getRepository(Rating::class)
        ->findAll();

        return $this->render('admin/moderation.html.twig', [
            'ratings' => $ratings,
        ]);
    }

    /**
     * @Route("/admin/remove_rating/{id}", name="remove_rating")
     */
    public function remove_rating(Rating $rating)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user == "anon." || !$user->getAdmin()){
            return $this->redirectToRoute('app_login');
        }

        $em = $this->getDoctrine()
        ->getManager();
        $em->remove($rating);
        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);

    }

}
