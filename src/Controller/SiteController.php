<?php

namespace App\Controller;

use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    
    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos() {
        return $this->render('site/apropos.html.twig');
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion() {
        return $this->render('site/connexion.html.twig');
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription() {
        return $this->render('site/inscription.html.twig');
    }

    /**
     * @Route("/series", name="series")
     */
    public function series() {

        $series = $this->getDoctrine()
        ->getRepository(Series::class)
        ->findBy(
            array(),
            array('title' => 'ASC'),
            10,
        );

        return $this->render('site/series.html.twig', [
            'series' => $series,
        ]);
    }


    /**
     * @Route("/series/{id}", name="poster_get", methods={"GET"})
     */
    public function poster(Series $serie) : Response
    {
        $poster = stream_get_contents($serie->getPoster());
        $rep = new Response($poster, 200,[
            "Content-type" => "image/jpeg",
        ]); 
        return $rep;
    }

}
