<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Series;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SerieController extends AbstractController
{
    /**
     * @Route("/serie/{id}", name="saisons_serie")
     */
    public function serie(Series $serie)
    {
        $saisons = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findBy(
            array('series' => $serie->getId()),
            array('number' => 'ASC'),
        );

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Episode::class);

        $episodes = array();
        foreach($saisons as $saison ){
            $episodes = array_merge($episodes, $repository->createQueryBuilder('e')
            ->where("e.season = ".$saison->getId())
            ->orderBy('e.number', 'ASC')
            ->getQuery()
            ->getResult());
        }


        return $this->render('serie/serie.html.twig', [
            'serie' => $serie,
            'saisons' => $saisons,
            'episodes' => $episodes,
        ]);
    }



    /**
     * @Route("/saison/{id}", name="episode_saison")
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

        return $this->render('serie/saison.html.twig', [
            'serie' => $serie,
            'saisons' => $season,
            'episodes' => $episode,
        ]);
    }
}
