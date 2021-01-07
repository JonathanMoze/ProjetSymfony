<?php

namespace App\Controller;

use App\Entity\Series;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class SeriesController extends AbstractController
{

    /**
     * @Route("/liste_series", name="series")
     */
    public function series(Request $requete, PaginatorInterface $paginator) {

        $donnees = $this->getDoctrine()
        ->getRepository(Series::class)
        ->findBy(
            array(),
            array('title' => 'ASC'),
        );

        $series=$paginator->paginate(
            $donnees,
            $requete->query->getInt('page',1),
            10
        );

        return $this->render('series/liste_series.html.twig', [
            'series' => $series,
        ]);
    }

    /**
     * @Route("/liste_series/{id}", name="poster_get", methods={"GET"})
     */
    public function poster(Series $serie) : Response
    {
        $poster = stream_get_contents($serie->getPoster());
        $rep = new Response($poster, 200,[
            "Content-type" => "image/jpeg",
        ]); 
        return $rep;
    }


    /**
     * @Route("/serie/{id}", name="saisons_serie", methods={"GET"})
     */
    public function serie(Series $serie)
    {
        $saisons = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findBy(
            array('series' => $serie->getId()),
            array('number' => 'ASC'),
        );

        return $this->render('series/serie.html.twig', [
            'serie' => $serie,
            'saisons' => $saisons,
        ]);
    }

    /**
     * @Route("/saison/{id}", name="episode_saison", methods={"GET"})
     */
    public function saison(Season $season )
    {
        $episode = $this->getDoctrine()
        ->getRepository(Episode::class)
        ->findBy(
            array('season' => $season->getId()),
            array('number' => 'ASC'),
        );
        $serie = $season->getSeries();

        return $this->render('series/saison.html.twig', [
            'serie' => $serie,
            'saisons' => $season,
            'episodes' => $episode,
        ]);
    }

}
