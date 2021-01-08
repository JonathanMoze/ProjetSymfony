<?php

namespace App\Controller;

use App\Entity\Series;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserSpaceController extends AbstractController
{
    /**
     * @Route("/mes_series", name="mes_series")
     */
    public function mes_series(Request $requete, PaginatorInterface $paginator): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();


        $mesSeries = $user->getSeries()
        ;
        $series=$paginator->paginate(
            $mesSeries,
            $requete->query->getInt('page',1),
            10
        );

        return $this->render('user_space/mes_series.html.twig', [
            'series' => $series,
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
        
        return $this->redirectToRoute('series');
    }

    /**
     * @Route("/user/removeSerie/{id}", name="remove_serie")
     */
    public function remove_serie(Series $serie)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user->removeSeries($serie);

        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('series');
    }
}
